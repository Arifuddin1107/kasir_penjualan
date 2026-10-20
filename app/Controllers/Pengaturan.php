<?php

namespace App\Controllers;

use App\Models\PengaturanModel;

class Pengaturan extends BaseController
{
    protected $pengaturanModel;

    public function __construct()
    {
        $this->pengaturanModel = new PengaturanModel();
    }

    public function index()
    {
        $settings = $this->pengaturanModel->getSettings();

        $data = [
            'title'    => 'Pengaturan Toko & Printer Struk — Antigravity Retail',
            'settings' => $settings,
        ];

        return view('admin/pengaturan/index', $data);
    }

    public function update()
    {
        $settings = $this->pengaturanModel->getSettings();

        $namaToko    = trim((string) $this->request->getPost('nama_toko'));
        $alamat      = trim((string) $this->request->getPost('alamat'));
        $noTelp      = trim((string) $this->request->getPost('no_telp'));
        $footerStruk = trim((string) $this->request->getPost('footer_struk'));
        $paperSize   = (string) $this->request->getPost('paper_size');

        $this->pengaturanModel->update($settings['id'], [
            'nama_toko'    => $namaToko ?: 'Antigravity Retail Mart',
            'alamat'       => $alamat,
            'no_telp'      => $noTelp,
            'footer_struk' => $footerStruk ?: 'Terima kasih atas kunjungan Anda',
            'paper_size'   => in_array($paperSize, ['58mm', '80mm'], true) ? $paperSize : '58mm',
        ]);

        return redirect()->to('/pengaturan')->with('success', 'Pengaturan toko dan preferensi cetak struk berhasil disimpan.');
    }
}

