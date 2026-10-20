<?php

namespace App\Controllers;

use App\Models\PenjualanModel;
use App\Models\ProdukModel;
use App\Models\KategoriModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    protected $penjualanModel;
    protected $produkModel;
    protected $kategoriModel;
    protected $userModel;

    public function __construct()
    {
        $this->penjualanModel = new PenjualanModel();
        $this->produkModel    = new ProdukModel();
        $this->kategoriModel  = new KategoriModel();
        $this->userModel      = new UserModel();
    }

    public function index()
    {
        $today = date('Y-m-d');
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');

        // Ringkasan Hari Ini
        $summaryToday = $this->penjualanModel->getFinancialSummary($today, $today);

        // Ringkasan Bulan Ini
        $summaryMonth = $this->penjualanModel->getFinancialSummary($startOfMonth, $endOfMonth);

        // Ringkasan Keseluruhan
        $summaryAll = $this->penjualanModel->getFinancialSummary();

        // Data Stok Kritis (FR-2.2)
        $lowStockProducts = $this->produkModel->getLowStockProducts();
        $lowStockCount = count($lowStockProducts);

        // Transaksi Terkini (5 terakhir)
        $recentSales = $this->penjualanModel->getWithKasir();
        $recentSales = array_slice($recentSales, 0, 6);

        // Data Grafik 7 Hari Terakhir
        $chartData = $this->penjualanModel->getSalesChartData(7);

        // Total Master Data
        $totalProduk = $this->produkModel->countAllResults();
        $totalKategori = $this->kategoriModel->countAllResults();
        $totalKasir = $this->userModel->where('role', 'kasir')->countAllResults();

        $data = [
            'title'            => 'Dashboard Analitik — Antigravity Retail',
            'summaryToday'     => $summaryToday,
            'summaryMonth'     => $summaryMonth,
            'summaryAll'       => $summaryAll,
            'lowStockProducts' => $lowStockProducts,
            'lowStockCount'    => $lowStockCount,
            'recentSales'      => $recentSales,
            'chartData'        => $chartData,
            'totalProduk'      => $totalProduk,
            'totalKategori'    => $totalKategori,
            'totalKasir'       => $totalKasir,
        ];

        return view('admin/dashboard', $data);
    }
}

