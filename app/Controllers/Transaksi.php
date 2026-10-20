<?php

namespace App\Controllers;

use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;
use App\Models\PengaturanModel;

class Transaksi extends BaseController
{
    protected $penjualanModel;
    protected $detailModel;
    protected $pengaturanModel;

    public function __construct()
    {
        $this->penjualanModel  = new PenjualanModel();
        $this->detailModel     = new PenjualanDetailModel();
        $this->pengaturanModel = new PengaturanModel();
    }

    /**
     * Tampilan Struk Thermal (58mm / 80mm) yang otomatis memanggil window.print() (FR-4.1)
     */
    public function struk($id)
    {
        $penjualan = $this->penjualanModel->getWithKasir($id);
        if (!$penjualan) {
            return redirect()->to('/pos')->with('error', 'Transaksi tidak ditemukan.');
        }

        $items    = $this->detailModel->getItemsByPenjualanId($id);
        $settings = $this->pengaturanModel->getSettings();

        $data = [
            'penjualan' => $penjualan,
            'items'     => $items,
            'settings'  => $settings,
            'paperSize' => $settings['paper_size'] ?? '58mm',
        ];

        return view('transaksi/struk', $data);
    }

    /**
     * API JSON Detail Transaksi
     */
    public function detailJson($id)
    {
        $penjualan = $this->penjualanModel->getWithKasir($id);
        if (!$penjualan) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Transaksi tidak ditemukan.',
            ]);
        }

        $items = $this->detailModel->getItemsByPenjualanId($id);

        return $this->response->setJSON([
            'status'    => 'success',
            'penjualan' => $penjualan,
            'items'     => $items,
        ]);
    }
}

