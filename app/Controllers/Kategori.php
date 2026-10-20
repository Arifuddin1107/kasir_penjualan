<?php

namespace App\Controllers;

use App\Models\KategoriModel;

class Kategori extends BaseController
{
    protected $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
    }

    public function index()
    {
        $kategori = $this->kategoriModel->getWithProductCount();

        $data = [
            'title'    => 'Manajemen Kategori — Antigravity Retail',
            'kategori' => $kategori,
        ];

        return view('admin/kategori/index', $data);
    }

    public function store()
    {
        $nama = trim((string) $this->request->getPost('nama_kategori'));

        if (empty($nama)) {
            return redirect()->back()->with('error', 'Nama kategori wajib diisi.');
        }

        $this->kategoriModel->insert(['nama_kategori' => $nama]);
        return redirect()->to('/kategori')->with('success', "Kategori \"{$nama}\" berhasil ditambahkan.");
    }

    public function update($id)
    {
        $nama = trim((string) $this->request->getPost('nama_kategori'));

        if (empty($nama)) {
            return redirect()->back()->with('error', 'Nama kategori tidak boleh kosong.');
        }

        $this->kategoriModel->update($id, ['nama_kategori' => $nama]);
        return redirect()->to('/kategori')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function delete($id)
    {
        try {
            $this->kategoriModel->delete($id);
            return redirect()->to('/kategori')->with('success', 'Kategori berhasil dihapus.');
        } catch (\Throwable $e) {
            return redirect()->to('/kategori')->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh produk terdaftar (Foreign Key RESTRICT).');
        }
    }
}

