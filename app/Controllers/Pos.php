<?php

namespace App\Controllers;

use App\Models\ProdukModel;
use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;
use App\Models\MutasiStokModel;
use App\Models\PengaturanModel;

class Pos extends BaseController
{
    protected $produkModel;
    protected $penjualanModel;
    protected $detailModel;
    protected $mutasiModel;
    protected $pengaturanModel;

    public function __construct()
    {
        $this->produkModel     = new ProdukModel();
        $this->penjualanModel  = new PenjualanModel();
        $this->detailModel     = new PenjualanDetailModel();
        $this->mutasiModel     = new MutasiStokModel();
        $this->pengaturanModel = new PengaturanModel();
    }

    /**
     * Tampilan Utama POS (Single Page POS Interface)
     */
    public function index()
    {
        $settings = $this->pengaturanModel->getSettings();
        $today = date('Y-m-d');
        
        // Ambil riwayat transaksi kasir hari ini
        $userId = (int) session()->get('user_id');
        $userRole = session()->get('role');

        $builder = $this->penjualanModel->where('tanggal >=', $today . ' 00:00:00')
            ->where('tanggal <=', $today . ' 23:59:59');

        if ($userRole === 'kasir') {
            $builder->where('user_id', $userId);
        }

        $todayTransactions = $builder->orderBy('id', 'DESC')->limit(15)->findAll();

        $data = [
            'title'             => 'Kasir POS — Antigravity Retail Mart',
            'settings'          => $settings,
            'todayTransactions' => $todayTransactions,
        ];

        return view('pos/index', $data);
    }

    /**
     * API Pencarian Produk Cepat untuk Barcode & Autocomplete (FR-3.2, < 200ms)
     */
    public function search()
    {
        $keyword = trim((string) $this->request->getGet('q'));

        if (empty($keyword)) {
            return $this->response->setJSON(['status' => 'success', 'data' => []]);
        }

        // Cari barcode tepat dahulu
        $exactProduct = $this->produkModel->findByBarcode($keyword);
        if ($exactProduct) {
            return $this->response->setJSON([
                'status'    => 'success',
                'exact'     => true,
                'data'      => [$exactProduct],
            ]);
        }

        // Jika bukan barcode tepat, cari via like query nama atau barcode
        $results = $this->produkModel->searchForPos($keyword);

        return $this->response->setJSON([
            'status' => 'success',
            'exact'  => false,
            'data'   => $results,
        ]);
    }

    /**
     * API Proses Checkout ACID (FR-3.3)
     */
    public function checkout()
    {
        $json = $this->request->getJSON(true);

        if (!$json) {
            $json = [
                'items'             => json_decode($this->request->getPost('items') ?? '[]', true),
                'bayar'             => (float) $this->request->getPost('bayar'),
                'metode_pembayaran' => (string) ($this->request->getPost('metode_pembayaran') ?: 'cash'),
                'catatan'           => (string) $this->request->getPost('catatan'),
            ];
        }

        $items            = $json['items'] ?? [];
        $bayar            = (float) ($json['bayar'] ?? 0);
        $metodePembayaran = $json['metode_pembayaran'] ?? 'cash';
        $catatan          = $json['catatan'] ?? null;

        if (empty($items) || !is_array($items)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Keranjang belanja masih kosong.',
            ]);
        }

        $userId = (int) session()->get('user_id');
        $db = \Config\Database::connect();

        // Mulai Transaksi Database CI4 (transBegin)
        $db->transBegin();

        try {
            $totalItem = 0;
            $totalOmzet = 0.00;
            $totalHpp = 0.00;
            $itemsToProcess = [];

            // 1. Validasi stok aktual dan locking untuk mencegah penjualan minus
            foreach ($items as $item) {
                $produkId = (int) ($item['id'] ?? 0);
                $qty = (int) ($item['qty'] ?? 0);

                if ($produkId <= 0 || $qty <= 0) {
                    throw new \Exception('Data item belanja tidak valid.');
                }

                // Ambil data produk terkini langsung dari DB dengan FOR UPDATE
                $produkRow = $db->query("SELECT * FROM produk WHERE id = ? FOR UPDATE", [$produkId])->getRowArray();

                if (!$produkRow) {
                    throw new \Exception("Produk dengan ID {$produkId} tidak ditemukan.");
                }

                if ((int) $produkRow['stok'] < $qty) {
                    throw new \Exception("Stok tidak mencukupi untuk \"{$produkRow['nama_produk']}\". Stok tersedia: {$produkRow['stok']}, diminta: {$qty}.");
                }

                $hargaBeli   = (float) $produkRow['harga_beli'];
                $hargaJual   = (float) $produkRow['harga_jual'];
                $subtotalOmzet = $qty * $hargaJual;
                $subtotalHpp   = $qty * $hargaBeli;
                $subtotalLaba  = $subtotalOmzet - $subtotalHpp;

                $totalItem  += $qty;
                $totalOmzet += $subtotalOmzet;
                $totalHpp   += $subtotalHpp;

                $itemsToProcess[] = [
                    'produk'         => $produkRow,
                    'qty'            => $qty,
                    'harga_beli'     => $hargaBeli,
                    'harga_jual'     => $hargaJual,
                    'subtotal_omzet' => $subtotalOmzet,
                    'subtotal_hpp'   => $subtotalHpp,
                    'subtotal_laba'  => $subtotalLaba,
                ];
            }

            // 2. Validasi Pembayaran
            if ($bayar < $totalOmzet && $metodePembayaran === 'cash') {
                throw new \Exception('Jumlah uang tunai yang dibayarkan kurang dari total belanja.');
            }

            $kembalian = ($metodePembayaran === 'cash') ? ($bayar - $totalOmzet) : 0.00;
            if ($metodePembayaran !== 'cash' && $bayar < $totalOmzet) {
                $bayar = $totalOmzet;
            }

            // FR-5.1: Laba Kotor = Total Omzet - Total HPP
            $labaKotor = $totalOmzet - $totalHpp;

            // 3. Simpan Header Penjualan
            $nomorFaktur = $this->penjualanModel->generateNomorFaktur();
            $now = date('Y-m-d H:i:s');

            $db->table('penjualan')->insert([
                'nomor_faktur'      => $nomorFaktur,
                'user_id'           => $userId,
                'tanggal'           => $now,
                'total_item'        => $totalItem,
                'total_omzet'       => $totalOmzet,
                'total_hpp'         => $totalHpp,
                'laba_kotor'        => $labaKotor,
                'bayar'             => $bayar,
                'kembalian'         => $kembalian,
                'metode_pembayaran' => $metodePembayaran,
                'catatan'           => $catatan,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);

            $penjualanId = $db->insertID();

            // 4. Simpan Rincian Penjualan, Kurangi Stok, & Catat Mutasi 'sale' (FR-2.3)
            foreach ($itemsToProcess as $proc) {
                $p = $proc['produk'];
                $qty = $proc['qty'];
                $stokSebelum = (int) $p['stok'];
                $stokSesudah = $stokSebelum - $qty;

                // Insert detail penjualan
                $db->table('penjualan_detail')->insert([
                    'penjualan_id'   => $penjualanId,
                    'produk_id'      => (int) $p['id'],
                    'barcode'        => $p['barcode'],
                    'nama_produk'    => $p['nama_produk'],
                    'harga_beli'     => $proc['harga_beli'],
                    'harga_jual'     => $proc['harga_jual'],
                    'qty'            => $qty,
                    'subtotal_omzet' => $proc['subtotal_omzet'],
                    'subtotal_hpp'   => $proc['subtotal_hpp'],
                    'subtotal_laba'  => $proc['subtotal_laba'],
                    'created_at'     => $now,
                ]);

                // Kurangi stok produk
                $db->table('produk')->where('id', $p['id'])->update([
                    'stok'       => $stokSesudah,
                    'updated_at' => $now,
                ]);

                // Catat mutasi stok tipe 'sale' (FR-2.3)
                $db->table('mutasi_stok')->insert([
                    'produk_id'    => (int) $p['id'],
                    'user_id'      => $userId,
                    'tipe'         => 'sale',
                    'kuantitas'    => $qty,
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSesudah,
                    'nomor_faktur' => $nomorFaktur,
                    'keterangan'   => "Penjualan Kasir Faktur {$nomorFaktur}",
                    'created_at'   => $now,
                ]);
            }

            // Commit transaksi jika seluruh operasi berhasil
            $db->transCommit();

            return $this->response->setJSON([
                'status'       => 'success',
                'message'      => 'Transaksi berhasil disimpan.',
                'penjualan_id' => $penjualanId,
                'nomor_faktur' => $nomorFaktur,
                'total_omzet'  => $totalOmzet,
                'bayar'        => $bayar,
                'kembalian'    => $kembalian,
                'tanggal'      => date('d/m/Y H:i', strtotime($now)),
            ]);
        } catch (\Throwable $e) {
            // Rollback jika terjadi kesalahan
            $db->transRollback();

            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}

