<?php

namespace App\Controllers;

use App\Models\MutasiStokModel;
use App\Models\ProdukModel;

class MutasiStok extends BaseController
{
    protected $mutasiModel;
    protected $produkModel;

    public function __construct()
    {
        $this->mutasiModel = new MutasiStokModel();
        $this->produkModel = new ProdukModel();
    }

    public function index()
    {
        $produkId  = $this->request->getGet('produk_id') ? (int) $this->request->getGet('produk_id') : null;
        $tipe      = $this->request->getGet('tipe');
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        $logs = $this->mutasiModel->getDetailedLogs($produkId, $tipe, $startDate, $endDate);
        $produkList = $this->produkModel->orderBy('nama_produk', 'ASC')->findAll();

        $data = [
            'title'       => 'Log Mutasi & Riwayat Stok — Antigravity Retail',
            'logs'        => $logs,
            'produkList'  => $produkList,
            'selectedPrd' => $produkId,
            'selectedTipe'=> $tipe,
            'startDate'   => $startDate,
            'endDate'     => $endDate,
        ];

        return view('admin/mutasi/index', $data);
    }

    /**
     * Penyesuaian stok manual / Restock (tipe: in, out, adjustment)
     */
    public function adjust()
    {
        $produkId   = (int) $this->request->getPost('produk_id');
        $tipe       = (string) $this->request->getPost('tipe'); // 'in', 'out', 'adjustment'
        $kuantitas  = (int) $this->request->getPost('kuantitas');
        $keterangan = trim((string) $this->request->getPost('keterangan'));

        if (!$produkId || !in_array($tipe, ['in', 'out', 'adjustment'], true) || $kuantitas <= 0) {
            return redirect()->back()->with('error', 'Parameter penyesuaian stok tidak valid.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Ambil data produk dengan locking (FOR UPDATE)
            $produk = $db->table('produk')->where('id', $produkId)->get()->getRowArray();
            if (!$produk) {
                throw new \Exception('Produk tidak ditemukan.');
            }

            $stokSebelum = (int) $produk['stok'];

            if ($tipe === 'in') {
                $stokSesudah = $stokSebelum + $kuantitas;
            } elseif ($tipe === 'out') {
                if ($kuantitas > $stokSebelum) {
                    throw new \Exception("Stok tidak mencukupi untuk pengeluaran barang. Stok saat ini: {$stokSebelum}.");
                }
                $stokSesudah = $stokSebelum - $kuantitas;
            } else { // adjustment (stok opname hasil hitung fisik)
                $stokSesudah = $kuantitas;
                $kuantitas = abs($stokSesudah - $stokSebelum);
            }

            // Update stok produk
            $db->table('produk')->where('id', $produkId)->update([
                'stok'       => $stokSesudah,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Catat log mutasi
            $refNumber = 'ADJ-' . date('Ymd-His');
            $db->table('mutasi_stok')->insert([
                'produk_id'    => $produkId,
                'user_id'      => (int) session()->get('user_id'),
                'tipe'         => $tipe,
                'kuantitas'    => $kuantitas,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'nomor_faktur' => $refNumber,
                'keterangan'   => $keterangan ?: 'Penyesuaian stok manual oleh Admin',
                'created_at'   => date('Y-m-d H:i:s'),
            ]);

            $db->transCommit();

            return redirect()->to('/mutasi')->with('success', "Mutasi stok produk \"{$produk['nama_produk']}\" berhasil dicatat.");
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}

