<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h1 style="font-size: 22px; font-weight: 800; color: var(--text-primary); letter-spacing: -0.02em;">Log Mutasi & Riwayat Pergerakan Stok</h1>
        <p style="font-size: 13.5px; color: var(--text-muted); margin-top: 2px;">Audit jejak pergerakan stok otomatis (penjualan kasir) dan manual (restock / opname).</p>
    </div>
    <div>
        <button type="button" class="antigravity-btn" onclick="openModal('modalAdjustStok')">
            <i class="fa-solid fa-boxes-packing"></i> Input Restock / Penyesuaian Manual
        </button>
    </div>
</div>

<!-- Filter Log Mutasi -->
<div class="antigravity-card" style="padding: 16px; margin-bottom: 20px;">
    <form action="<?= base_url('mutasi') ?>" method="GET" style="display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 180px;">
            <label class="form-label">Filter Produk</label>
            <select name="produk_id" class="form-select">
                <option value="">Semua Produk</option>
                <?php foreach ($produkList as $pr): ?>
                    <option value="<?= $pr['id'] ?>" <?= ($selectedPrd == $pr['id']) ? 'selected' : '' ?>>
                        <?= esc($pr['nama_produk']) ?> (<?= esc($pr['barcode']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="flex: 1; min-width: 140px;">
            <label class="form-label">Tipe Mutasi</label>
            <select name="tipe" class="form-select">
                <option value="">Semua Tipe</option>
                <option value="in" <?= ($selectedTipe === 'in') ? 'selected' : '' ?>>Barang Masuk (In)</option>
                <option value="sale" <?= ($selectedTipe === 'sale') ? 'selected' : '' ?>>Penjualan POS (Sale)</option>
                <option value="out" <?= ($selectedTipe === 'out') ? 'selected' : '' ?>>Barang Keluar/Rusak (Out)</option>
                <option value="adjustment" <?= ($selectedTipe === 'adjustment') ? 'selected' : '' ?>>Opname Fisik (Adjustment)</option>
            </select>
        </div>

        <div style="flex: 1; min-width: 140px;">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="start_date" class="form-control" value="<?= esc($startDate ?? '') ?>">
        </div>

        <div style="flex: 1; min-width: 140px;">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="end_date" class="form-control" value="<?= esc($endDate ?? '') ?>">
        </div>

        <div>
            <button type="submit" class="antigravity-btn" style="height: 42px;">
                <i class="fa-solid fa-filter"></i> Terapkan
            </button>
            <a href="<?= base_url('mutasi') ?>" class="antigravity-btn-outline" style="height: 42px; margin-left: 4px;">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Table Card (FR-2.3) -->
<div class="antigravity-card">
    <div class="table-responsive">
        <table class="antigravity-table">
            <thead>
                <tr>
                    <th>Waktu Mutasi</th>
                    <th>No. Faktur / Referensi</th>
                    <th>Nama Produk</th>
                    <th>Tipe Mutasi</th>
                    <th style="text-align: right;">Stok Awal</th>
                    <th style="text-align: right;">Kuantitas</th>
                    <th style="text-align: right;">Stok Akhir</th>
                    <th>Petugas / User</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 36px;">
                            <i class="fa-solid fa-clock-rotate-left" style="font-size: 28px; color: #9CA3AF; margin-bottom: 8px; display: block;"></i>
                            Tidak ada data mutasi yang tercatat untuk filter ini.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
                            <td>
                                <code><?= esc($log['nomor_faktur'] ?? '-') ?></code>
                            </td>
                            <td>
                                <strong><?= esc($log['nama_produk']) ?></strong>
                                <div style="font-size: 11px; color: var(--text-muted);"><?= esc($log['barcode']) ?></div>
                            </td>
                            <td>
                                <?php if ($log['tipe'] === 'in'): ?>
                                    <span class="badge badge-success"><i class="fa-solid fa-arrow-down"></i> MASUK (IN)</span>
                                <?php elseif ($log['tipe'] === 'sale'): ?>
                                    <span class="badge badge-info"><i class="fa-solid fa-cart-shopping"></i> KASIR (SALE)</span>
                                <?php elseif ($log['tipe'] === 'out'): ?>
                                    <span class="badge badge-danger"><i class="fa-solid fa-arrow-up"></i> KELUAR (OUT)</span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><i class="fa-solid fa-sliders"></i> OPNAME (ADJ)</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;"><?= $log['stok_sebelum'] ?></td>
                            <td style="text-align: right; font-weight: 800;">
                                <?php if ($log['tipe'] === 'in'): ?>
                                    <span style="color: #059669;">+<?= $log['kuantitas'] ?></span>
                                <?php elseif ($log['tipe'] === 'out' || $log['tipe'] === 'sale'): ?>
                                    <span style="color: #DC2626;">-<?= $log['kuantitas'] ?></span>
                                <?php else: ?>
                                    <span><?= $log['kuantitas'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right; font-weight: 800; color: var(--text-primary);"><?= $log['stok_sesudah'] ?></td>
                            <td>
                                <span style="font-size: 12px; font-weight: 600; color: #4B5563;">
                                    <?= esc($log['nama_user'] ?? 'System') ?>
                                </span>
                            </td>
                            <td style="font-size: 12px; color: var(--text-muted);"><?= esc($log['keterangan'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Penyesuaian Stok / Restock Manual -->
<div id="modalAdjustStok" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fa-solid fa-boxes-packing" style="color: var(--accent-green);"></i> Restock / Penyesuaian Stok Manual</h3>
            <button type="button" class="modal-close" onclick="closeModal('modalAdjustStok')">&times;</button>
        </div>
        <form action="<?= base_url('mutasi/adjust') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Pilih Produk *</label>
                    <select name="produk_id" class="form-select" required>
                        <option value="">-- Pilih Produk --</option>
                        <?php foreach ($produkList as $pr): ?>
                            <option value="<?= $pr['id'] ?>">
                                <?= esc($pr['nama_produk']) ?> (Stok saat ini: <?= $pr['stok'] ?> <?= esc($pr['satuan']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Tipe Penyesuaian *</label>
                        <select name="tipe" class="form-select" required>
                            <option value="in">Stok Masuk / Restock Pembelian (IN)</option>
                            <option value="out">Barang Keluar / Rusak / Hilang (OUT)</option>
                            <option value="adjustment">Stok Opname (Set Hasil Hitung Fisik)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jumlah Kuantitas *</label>
                        <input type="number" name="kuantitas" class="form-control" placeholder="Contoh: 10" min="1" required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Keterangan / Nomor Referensi</label>
                    <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Restock Supplier PT ABC / Rusak saat unboxing">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="antigravity-btn-outline" onclick="closeModal('modalAdjustStok')">Batal</button>
                <button type="submit" class="antigravity-btn"><i class="fa-solid fa-check"></i> Proses Mutasi</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

