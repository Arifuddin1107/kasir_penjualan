<?php

namespace App\Controllers;

use App\Models\ProdukModel;
use App\Models\KategoriModel;
use App\Models\MutasiStokModel;

class Produk extends BaseController
{
    protected $produkModel;
    protected $kategoriModel;
    protected $mutasiModel;

    public function __construct()
    {
        $this->produkModel   = new ProdukModel();
        $this->kategoriModel = new KategoriModel();
        $this->mutasiModel   = new MutasiStokModel();
    }

    public function index()
    {
        $kategoriId = $this->request->getGet('kategori_id');
        $filterStok = $this->request->getGet('stok_status'); // all, low

        $builder = $this->produkModel->select('produk.*, kategori.nama_kategori')
            ->join('kategori', 'kategori.id = produk.kategori_id', 'left');

        if (!empty($kategoriId)) {
            $builder->where('produk.kategori_id', (int) $kategoriId);
        }

        if ($filterStok === 'low') {
            $builder->where('produk.stok <= produk.min_stock');
        }

        $produk = $builder->orderBy('produk.nama_produk', 'ASC')->findAll();
        $kategori = $this->kategoriModel->orderBy('nama_kategori', 'ASC')->findAll();

        $data = [
            'title'      => 'Master Produk & Inventaris — Antigravity Retail',
            'produk'     => $produk,
            'kategori'   => $kategori,
            'selectedKat'=> $kategoriId,
            'selectedStok'=> $filterStok,
        ];

        return view('admin/produk/index', $data);
    }

    public function store()
    {
        $barcode   = trim((string) $this->request->getPost('barcode'));
        $nama      = trim((string) $this->request->getPost('nama_produk'));
        $kategoriId= $this->request->getPost('kategori_id') ?: null;
        $hargaBeli = (float) $this->request->getPost('harga_beli');
        $hargaJual = (float) $this->request->getPost('harga_jual');
        $stok      = (int) $this->request->getPost('stok');
        $minStock  = (int) ($this->request->getPost('min_stock') ?: 5);
        $satuan    = trim((string) $this->request->getPost('satuan')) ?: 'pcs';
        $deskripsi = trim((string) $this->request->getPost('deskripsi'));

        // Validasi
        if (empty($barcode) || empty($nama)) {
            return redirect()->back()->withInput()->with('error', 'Barcode dan Nama Produk wajib diisi.');
        }

        // Cek barcode unik
        if ($this->produkModel->findByBarcode($barcode)) {
            return redirect()->back()->withInput()->with('error', "Barcode {$barcode} sudah digunakan oleh produk lain.");
        }

        if ($hargaJual < $hargaBeli) {
            return redirect()->back()->withInput()->with('error', 'Harga Jual tidak boleh lebih rendah dari Harga Beli (HPP).');
        }

        $dataProduk = [
            'barcode'     => $barcode,
            'nama_produk' => $nama,
            'kategori_id' => $kategoriId,
            'harga_beli'  => $hargaBeli,
            'harga_jual'  => $hargaJual,
            'stok'        => $stok,
            'min_stock'   => $minStock,
            'satuan'      => $satuan,
            'deskripsi'   => $deskripsi,
        ];

        $this->produkModel->insert($dataProduk);
        $newId = $this->produkModel->insertID();

        // Catat mutasi stok awal jika ada stok > 0
        if ($stok > 0) {
            $this->mutasiModel->logMutation([
                'produk_id'    => $newId,
                'user_id'      => (int) session()->get('user_id'),
                'tipe'         => 'in',
                'kuantitas'    => $stok,
                'stok_sebelum' => 0,
                'stok_sesudah' => $stok,
                'nomor_faktur' => 'INIT-PRODUK',
                'keterangan'   => 'Stok awal penambahan produk baru',
            ]);
        }

        return redirect()->to('/produk')->with('success', "Produk \"{$nama}\" berhasil disimpan.");
    }

    public function update($id)
    {
        $produk = $this->produkModel->find($id);
        if (!$produk) {
            return redirect()->to('/produk')->with('error', 'Produk tidak ditemukan.');
        }

        $barcode   = trim((string) $this->request->getPost('barcode'));
        $nama      = trim((string) $this->request->getPost('nama_produk'));
        $kategoriId= $this->request->getPost('kategori_id') ?: null;
        $hargaBeli = (float) $this->request->getPost('harga_beli');
        $hargaJual = (float) $this->request->getPost('harga_jual');
        $minStock  = (int) ($this->request->getPost('min_stock') ?: 5);
        $satuan    = trim((string) $this->request->getPost('satuan')) ?: 'pcs';
        $deskripsi = trim((string) $this->request->getPost('deskripsi'));

        // Cek barcode jika berubah
        $existing = $this->produkModel->where('barcode', $barcode)->where('id !=', $id)->first();
        if ($existing) {
            return redirect()->back()->withInput()->with('error', "Barcode {$barcode} sudah dipakai produk lain.");
        }

        $this->produkModel->update($id, [
            'barcode'     => $barcode,
            'nama_produk' => $nama,
            'kategori_id' => $kategoriId,
            'harga_beli'  => $hargaBeli,
            'harga_jual'  => $hargaJual,
            'min_stock'   => $minStock,
            'satuan'      => $satuan,
            'deskripsi'   => $deskripsi,
        ]);

        return redirect()->to('/produk')->with('success', "Data produk \"{$nama}\" berhasil diperbarui.");
    }

    public function delete($id)
    {
        $produk = $this->produkModel->find($id);
        if (!$produk) {
            return redirect()->to('/produk')->with('error', 'Produk tidak ditemukan.');
        }

        try {
            $this->produkModel->delete($id);
            return redirect()->to('/produk')->with('success', "Produk \"{$produk['nama_produk']}\" berhasil dihapus.");
        } catch (\Throwable $e) {
            return redirect()->to('/produk')->with('error', 'Gagal menghapus produk karena data ini memiliki relasi transaksi / mutasi aktif (Foreign Key RESTRICT).');
        }
    }
}

