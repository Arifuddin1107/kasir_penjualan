<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h1 style="font-size: 22px; font-weight: 800; color: var(--text-primary); letter-spacing: -0.02em;">Laporan Transaksi Global & Analitik Finansial</h1>
        <p style="font-size: 13.5px; color: var(--text-muted); margin-top: 2px;">Rekapitulasi omzet berkala, audit margin laba kotor, dan ekspor spreadsheet.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <!-- Tombol Ekspor Excel FR-5.2 -->
        <a href="<?= base_url('laporan/export?start_date=' . $startDate . '&end_date=' . $endDate) ?>" class="antigravity-btn" style="background: #047857;">
            <i class="fa-solid fa-file-excel"></i> Ekspor Excel (.xlsx)
        </a>
    </div>
</div>

<!-- Filter Periode Tanggal -->
<div class="antigravity-card" style="padding: 16px; margin-bottom: 20px;">
    <form action="<?= base_url('laporan') ?>" method="GET" style="display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 160px;">
            <label class="form-label">Tanggal Mulai</label>
            <input type="date" name="start_date" class="form-control" value="<?= esc($startDate) ?>" required>
        </div>

        <div style="flex: 1; min-width: 160px;">
            <label class="form-label">Tanggal Selesai</label>
            <input type="date" name="end_date" class="form-control" value="<?= esc($endDate) ?>" required>
        </div>

        <div>
            <button type="submit" class="antigravity-btn" style="height: 42px;">
                <i class="fa-solid fa-filter"></i> Tampilkan Rekap
            </button>
            <a href="<?= base_url('laporan') ?>" class="antigravity-btn-outline" style="height: 42px; margin-left: 4px;">
                Bulan Ini
            </a>
        </div>
    </form>
</div>

<!-- Financial Summary Cards (FR-5.1) -->
<div class="grid-4" style="margin-bottom: 24px;">
    <div class="antigravity-card" style="margin-bottom: 0;">
        <span style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">Total Akumulasi Omzet</span>
        <div style="font-size: 22px; font-weight: 800; color: var(--text-primary); margin-top: 4px;">
            Rp <?= number_format($summary['total_omzet'], 0, ',', '.') ?>
        </div>
        <div style="margin-top: 6px; font-size: 12px; color: var(--text-muted);">
            Dari <b><?= $summary['total_transaksi'] ?></b> transaksi
        </div>
    </div>

    <div class="antigravity-card" style="margin-bottom: 0;">
        <span style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">Total Modal (HPP)</span>
        <div style="font-size: 22px; font-weight: 800; color: #4B5563; margin-top: 4px;">
            Rp <?= number_format($summary['total_hpp'], 0, ',', '.') ?>
        </div>
        <div style="margin-top: 6px; font-size: 12px; color: var(--text-muted);">
            Harga Pokok Penjualan
        </div>
    </div>

    <div class="antigravity-card" style="margin-bottom: 0; border-color: #6EE7B7; background: #F0FDF4;">
        <span style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #047857;">Total Laba Kotor</span>
        <div style="font-size: 22px; font-weight: 800; color: #059669; margin-top: 4px;">
            Rp <?= number_format($summary['laba_kotor'], 0, ',', '.') ?>
        </div>
        <div style="margin-top: 6px; font-size: 12px; color: #065F46;">
            Omzet - Total HPP (FR-5.1)
        </div>
    </div>

    <div class="antigravity-card" style="margin-bottom: 0;">
        <span style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted);">Rasio Margin Keuntungan</span>
        <div style="font-size: 22px; font-weight: 800; color: #059669; margin-top: 4px;">
            <?= $marginPersen ?>%
        </div>
        <div style="margin-top: 6px; font-size: 12px; color: var(--text-muted);">
            Profit Margin terhadap Omzet
        </div>
    </div>
</div>

<!-- Transactions Table Card -->
<div class="antigravity-card">
    <div class="antigravity-card-header">
        <h2 class="card-title">
            <i class="fa-solid fa-list-check" style="color: var(--accent-green);"></i> Rincian Seluruh Transaksi Periode <?= esc($startDate) ?> s/d <?= esc($endDate) ?>
        </h2>
    </div>

    <div class="table-responsive">
        <table class="antigravity-table">
            <thead>
                <tr>
                    <th>No. Faktur</th>
                    <th>Tanggal & Jam</th>
                    <th>Kasir</th>
                    <th style="text-align: right;">Total Item</th>
                    <th style="text-align: right;">Total Omzet</th>
                    <th style="text-align: right;">Total HPP</th>
                    <th style="text-align: right;">Laba Kotor</th>
                    <th>Metode</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transaksi)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 36px;">
                            <i class="fa-solid fa-calendar-xmark" style="font-size: 28px; color: #9CA3AF; margin-bottom: 8px; display: block;"></i>
                            Tidak ada transaksi yang tercatat pada rentang tanggal tersebut.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transaksi as $tx): ?>
                        <tr>
                            <td>
                                <strong><?= esc($tx['nomor_faktur']) ?></strong>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($tx['tanggal'])) ?></td>
                            <td>
                                <span class="badge badge-info">
                                    <?= esc($tx['nama_kasir'] ?? 'System') ?>
                                </span>
                            </td>
                            <td style="text-align: right;"><?= $tx['total_item'] ?></td>
                            <td style="text-align: right; font-weight: 700;">
                                Rp <?= number_format($tx['total_omzet'], 0, ',', '.') ?>
                            </td>
                            <td style="text-align: right; color: var(--text-muted);">
                                Rp <?= number_format($tx['total_hpp'], 0, ',', '.') ?>
                            </td>
                            <td style="text-align: right; color: #059669; font-weight: 700;">
                                +Rp <?= number_format($tx['laba_kotor'], 0, ',', '.') ?>
                            </td>
                            <td>
                                <span class="badge badge-success"><?= strtoupper(esc($tx['metode_pembayaran'])) ?></span>
                            </td>
                            <td style="text-align: right;">
                                <a href="<?= base_url('transaksi/struk/' . $tx['id']) ?>" target="_blank" class="antigravity-btn-outline btn-sm">
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

<?= $this->endSection() ?>

