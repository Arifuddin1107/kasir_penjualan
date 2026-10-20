<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Header Page Title & Action -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h1 style="font-size: 22px; font-weight: 800; color: var(--text-primary); letter-spacing: -0.02em;">Dashboard Eksekutif & Finansial</h1>
        <p style="font-size: 13.5px; color: var(--text-muted); margin-top: 2px;">Ringkasan performa omzet kasir, margin laba kotor, dan kesehatan inventaris.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="<?= base_url('pos') ?>" class="antigravity-btn">
            <i class="fa-solid fa-cash-register"></i> Buka Layar Kasir
        </a>
        <a href="<?= base_url('laporan') ?>" class="antigravity-btn-outline">
            <i class="fa-solid fa-file-invoice"></i> Rekap Laporan
        </a>
    </div>
</div>

<!-- Warning Banner: Peringatan Stok Menipis (FR-2.2) -->
<?php if ($lowStockCount > 0): ?>
    <div class="alert alert-warning" style="display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 18px;"></i>
            <div>
                <strong>Perhatian Stok Kritis!</strong> Ada <b><?= $lowStockCount ?> produk</b> yang stoknya telah mencapai atau di bawah batas minimum (<code>stok ≤ min_stock</code>).
            </div>
        </div>
        <a href="<?= base_url('produk?stok_status=low') ?>" class="antigravity-btn-outline btn-sm" style="background: white; border-color: #FDE68A; color: #B45309;">
            Tinjau Stok Sekarang &rarr;
        </a>
    </div>
<?php endif; ?>

<!-- Metric Cards Grid (FR-5.1: Laba Kotor = Total Omzet - Total HPP) -->
<div class="grid-4" style="margin-bottom: 24px;">
    <!-- Omzet Hari Ini -->
    <div class="antigravity-card" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <span style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">Omzet Hari Ini</span>
                <div style="font-size: 24px; font-weight: 800; color: var(--text-primary); margin-top: 4px;">
                    Rp <?= number_format($summaryToday['total_omzet'], 0, ',', '.') ?>
                </div>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #ECFDF5; border: 1px solid #A7F3D0; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa-solid fa-rupiah-sign"></i>
            </div>
        </div>
        <div style="margin-top: 10px; font-size: 12px; color: var(--text-muted); display: flex; justify-content: space-between;">
            <span>Transaksi: <b><?= $summaryToday['total_transaksi'] ?></b></span>
            <span>Bulan Ini: <b>Rp <?= number_format($summaryMonth['total_omzet'], 0, ',', '.') ?></b></span>
        </div>
    </div>

    <!-- Total HPP Hari Ini -->
    <div class="antigravity-card" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <span style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">Total HPP (Modal)</span>
                <div style="font-size: 24px; font-weight: 800; color: #374151; margin-top: 4px;">
                    Rp <?= number_format($summaryToday['total_hpp'], 0, ',', '.') ?>
                </div>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #F3F4F6; border: 1px solid #E5E7EB; color: #4B5563; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa-solid fa-boxes-packing"></i>
            </div>
        </div>
        <div style="margin-top: 10px; font-size: 12px; color: var(--text-muted); display: flex; justify-content: space-between;">
            <span>HPP Hari Ini</span>
            <span>Bulan Ini: <b>Rp <?= number_format($summaryMonth['total_hpp'], 0, ',', '.') ?></b></span>
        </div>
    </div>

    <!-- Laba Kotor Hari Ini (FR-5.1) -->
    <div class="antigravity-card" style="margin-bottom: 0; border-color: #6EE7B7; background: linear-gradient(to bottom, #FFFFFF 0%, #F0FDF4 100%);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <span style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #047857;">Laba Kotor (Hari Ini)</span>
                <div style="font-size: 24px; font-weight: 800; color: #059669; margin-top: 4px;">
                    Rp <?= number_format($summaryToday['laba_kotor'], 0, ',', '.') ?>
                </div>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #D1FAE5; border: 1px solid #6EE7B7; color: #047857; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa-solid fa-chart-line"></i>
            </div>
        </div>
        <div style="margin-top: 10px; font-size: 12px; color: #065F46; display: flex; justify-content: space-between;">
            <?php 
                $marginHariIni = ($summaryToday['total_omzet'] > 0) 
                    ? round(($summaryToday['laba_kotor'] / $summaryToday['total_omzet']) * 100, 1) 
                    : 0;
            ?>
            <span>Margin: <b><?= $marginHariIni ?>%</b></span>
            <span>Bulan Ini: <b>Rp <?= number_format($summaryMonth['laba_kotor'], 0, ',', '.') ?></b></span>
        </div>
    </div>

    <!-- Kesehatan Stok / Total Produk -->
    <div class="antigravity-card" style="margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <span style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">Status Inventaris</span>
                <div style="font-size: 24px; font-weight: 800; color: var(--text-primary); margin-top: 4px;">
                    <?= $totalProduk ?> <span style="font-size: 14px; font-weight: 600; color: var(--text-muted);">SKU</span>
                </div>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: <?= $lowStockCount > 0 ? '#FEF2F2' : '#ECFDF5' ?>; border: 1px solid <?= $lowStockCount > 0 ? '#FECACA' : '#A7F3D0' ?>; color: <?= $lowStockCount > 0 ? '#DC2626' : '#059669' ?>; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa-solid <?= $lowStockCount > 0 ? 'fa-triangle-exclamation' : 'fa-check' ?>"></i>
            </div>
        </div>
        <div style="margin-top: 10px; font-size: 12px; color: var(--text-muted); display: flex; justify-content: space-between;">
            <span>Stok Kritis: <b style="color: <?= $lowStockCount > 0 ? '#DC2626' : '#059669' ?>;"><?= $lowStockCount ?> item</b></span>
            <span>Kategori: <b><?= $totalKategori ?></b></span>
        </div>
    </div>
</div>

<!-- Section: Grafik Performa & Ringkasan -->
<div class="grid-2" style="margin-bottom: 24px;">
    <!-- Chart 7 Hari Terakhir -->
    <div class="antigravity-card" style="margin-bottom: 0;">
        <div class="antigravity-card-header">
            <h2 class="card-title">
                <i class="fa-solid fa-chart-column" style="color: var(--accent-green);"></i> Tren Omzet & Laba (7 Hari Terakhir)
            </h2>
            <span style="font-size: 12px; color: var(--text-muted);">Analitik Harian</span>
        </div>
        <div style="height: 260px; position: relative;">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Produk dengan Stok Menipis (FR-2.2) -->
    <div class="antigravity-card" style="margin-bottom: 0;">
        <div class="antigravity-card-header">
            <h2 class="card-title">
                <i class="fa-solid fa-bell" style="color: #D97706;"></i> Peringatan Stok Menipis (Stok ≤ Min)
            </h2>
            <a href="<?= base_url('mutasi') ?>" class="antigravity-btn-outline btn-sm">
                <i class="fa-solid fa-plus"></i> Restock
            </a>
        </div>

        <div class="table-responsive" style="max-height: 260px; overflow-y: auto;">
            <table class="antigravity-table">
                <thead>
                    <tr>
                        <th>Barcode</th>
                        <th>Produk</th>
                        <th>Stok</th>
                        <th>Min</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lowStockProducts)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px;">
                                <i class="fa-solid fa-circle-check" style="color: #059669; font-size: 24px; margin-bottom: 8px; display: block;"></i>
                                Seluruh stok produk aman dan berada di atas batas minimum.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach (array_slice($lowStockProducts, 0, 5) as $lsp): ?>
                            <tr>
                                <td><code><?= esc($lsp['barcode']) ?></code></td>
                                <td><strong><?= esc($lsp['nama_produk']) ?></strong></td>
                                <td style="color: #DC2626; font-weight: 800;"><?= $lsp['stok'] ?> <?= esc($lsp['satuan']) ?></td>
                                <td><?= $lsp['min_stock'] ?></td>
                                <td>
                                    <span class="badge badge-danger">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Kritis
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Section: Transaksi Penjualan Terkini -->
<div class="antigravity-card">
    <div class="antigravity-card-header">
        <h2 class="card-title">
            <i class="fa-solid fa-receipt" style="color: var(--accent-green);"></i> Transaksi Penjualan Terakhir
        </h2>
        <a href="<?= base_url('laporan') ?>" class="antigravity-btn-outline btn-sm">
            Lihat Semua Laporan &rarr;
        </a>
    </div>

    <div class="table-responsive">
        <table class="antigravity-table">
            <thead>
                <tr>
                    <th>No Faktur</th>
                    <th>Waktu</th>
                    <th>Kasir</th>
                    <th>Total Item</th>
                    <th>Total Omzet</th>
                    <th>Total HPP</th>
                    <th>Laba Kotor</th>
                    <th>Metode</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentSales)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            Belum ada riwayat transaksi penjualan yang tercatat.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentSales as $sale): ?>
                        <tr>
                            <td>
                                <strong style="color: var(--text-primary);"><?= esc($sale['nomor_faktur']) ?></strong>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($sale['tanggal'])) ?></td>
                            <td>
                                <span class="badge badge-info">
                                    <i class="fa-solid fa-user"></i> <?= esc($sale['nama_kasir'] ?? 'Kasir') ?>
                                </span>
                            </td>
                            <td><?= $sale['total_item'] ?> item</td>
                            <td><strong>Rp <?= number_format($sale['total_omzet'], 0, ',', '.') ?></strong></td>
                            <td style="color: var(--text-muted);">Rp <?= number_format($sale['total_hpp'], 0, ',', '.') ?></td>
                            <td style="color: #059669; font-weight: 700;">+ Rp <?= number_format($sale['laba_kotor'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge badge-success"><?= strtoupper(esc($sale['metode_pembayaran'])) ?></span>
                            </td>
                            <td>
                                <a href="<?= base_url('transaksi/struk/' . $sale['id']) ?>" target="_blank" class="antigravity-btn-outline btn-sm" title="Cetak Ulang Struk">
                                    <i class="fa-solid fa-print"></i> Struk
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Inisialisasi Chart.js untuk Tren Omzet & Laba
    const chartLabels = <?= json_encode(array_column($chartData, 'tanggal')) ?>;
    const omzetData = <?= json_encode(array_column($chartData, 'omzet')) ?>;
    const labaData = <?= json_encode(array_column($chartData, 'laba_kotor')) ?>;

    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [
                {
                    label: 'Omzet Penjualan (Rp)',
                    data: omzetData,
                    backgroundColor: '#059669',
                    borderRadius: 6,
                },
                {
                    label: 'Laba Kotor (Rp)',
                    data: labaData,
                    backgroundColor: '#A7F3D0',
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value >= 1000000 ? (value/1000000) + 'jt' : (value/1000) + 'rb');
                        }
                    },
                    grid: { color: '#F3F4F6' }
                },
                x: { grid: { display: false } }
            }
        }
    });
</script>

<?= $this->endSection() ?>

