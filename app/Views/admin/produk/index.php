<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h1 style="font-size: 22px; font-weight: 800; color: var(--text-primary); letter-spacing: -0.02em;">Katalog Master Produk & Inventaris</h1>
        <p style="font-size: 13.5px; color: var(--text-muted); margin-top: 2px;">Kelola barcode, HPP modal, harga jual, stok fisik, dan batas minimum stok.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <button type="button" class="antigravity-btn" onclick="openModal('modalAddProduk')">
            <i class="fa-solid fa-plus"></i> Tambah Produk Baru
        </button>
    </div>
</div>

<!-- Filter Bar -->
<div class="antigravity-card" style="padding: 16px; margin-bottom: 20px;">
    <form action="<?= base_url('produk') ?>" method="GET" style="display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 200px;">
            <label class="form-label">Kategori</label>
            <select name="kategori_id" class="form-select" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                <?php foreach ($kategori as $kat): ?>
                    <option value="<?= $kat['id'] ?>" <?= ($selectedKat == $kat['id']) ? 'selected' : '' ?>>
                        <?= esc($kat['nama_kategori']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="flex: 1; min-width: 200px;">
            <label class="form-label">Status Inventaris</label>
            <select name="stok_status" class="form-select" onchange="this.form.submit()">
                <option value="" <?= empty($selectedStok) ? 'selected' : '' ?>>Semua Stok</option>
                <option value="low" <?= ($selectedStok === 'low') ? 'selected' : '' ?>>⚠️ Stok Menipis (Stok ≤ Min)</option>
            </select>
        </div>

        <div>
            <a href="<?= base_url('produk') ?>" class="antigravity-btn-outline" style="height: 42px;">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        </div>
    </form>
</div>

<!-- Product Table Card -->
<div class="antigravity-card">
    <div class="table-responsive">
        <table class="antigravity-table">
            <thead>
                <tr>
                    <th>Barcode</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga Beli (HPP)</th>
                    <th>Harga Jual</th>
                    <th>Margin / Item</th>
                    <th>Stok Aktual</th>
                    <th>Min. Stok</th>
                    <th>Status Stok</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($produk)): ?>
                    <tr>
                        <td colspan="10" style="text-align: center; color: var(--text-muted); padding: 36px;">
                            <i class="fa-solid fa-box-open" style="font-size: 28px; color: #9CA3AF; margin-bottom: 8px; display: block;"></i>
                            Tidak ada produk yang sesuai dengan filter.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($produk as $p): ?>
                        <?php 
                            $isLowStock = ((int) $p['stok'] <= (int) $p['min_stock']);
                            $marginNominal = (float) $p['harga_jual'] - (float) $p['harga_beli'];
                            $marginPersen = ($p['harga_jual'] > 0) ? round(($marginNominal / $p['harga_jual']) * 100, 1) : 0;
                        ?>
                        <tr style="<?= $isLowStock ? 'background-color: #FEF2F2;' : '' ?>">
                            <td>
                                <code><?= esc($p['barcode']) ?></code>
                            </td>
                            <td>
                                <strong><?= esc($p['nama_produk']) ?></strong>
                                <?php if (!empty($p['deskripsi'])): ?>
                                    <div style="font-size: 11.5px; color: var(--text-muted);"><?= esc($p['deskripsi']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-info"><?= esc($p['nama_kategori'] ?? 'Umum') ?></span>
                            </td>
                            <td>Rp <?= number_format($p['harga_beli'], 0, ',', '.') ?></td>
                            <td><strong>Rp <?= number_format($p['harga_jual'], 0, ',', '.') ?></strong></td>
                            <td>
                                <span style="color: #059669; font-weight: 700;">+Rp <?= number_format($marginNominal, 0, ',', '.') ?></span>
                                <span style="font-size: 11px; color: var(--text-muted);">(<?= $marginPersen ?>%)</span>
                            </td>
                            <td>
                                <span style="font-size: 14px; font-weight: 800; color: <?= $isLowStock ? '#DC2626' : 'var(--text-primary)' ?>;">
                                    <?= $p['stok'] ?>
                                </span>
                                <span style="font-size: 11.5px; color: var(--text-muted);"><?= esc($p['satuan']) ?></span>
                            </td>
                            <td><?= $p['min_stock'] ?></td>
                            <td>
                                <!-- FR-2.2: Indikator visual real-time bila stok <= min_stock -->
                                <?php if ($isLowStock): ?>
                                    <span class="badge badge-danger">
                                        <i class="fa-solid fa-triangle-exclamation"></i> Menipis
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-success">
                                        <i class="fa-solid fa-circle-check"></i> Aman
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <button type="button" class="antigravity-btn-outline btn-sm" onclick='editProduk(<?= json_encode($p) ?>)' title="Ubah Produk">
                                        <i class="fa-solid fa-pen-to-square"></i> Ubah
                                    </button>
                                    <a href="<?= base_url('produk/delete/' . $p['id']) ?>" class="antigravity-btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini? Tindakan tidak dapat dibatalkan jika belum memiliki riwayat mutasi/transaksi.')" title="Hapus Produk">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Produk (FR-2.1) -->
<div id="modalAddProduk" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fa-solid fa-plus-circle" style="color: var(--accent-green);"></i> Tambah Produk Baru</h3>
            <button type="button" class="modal-close" onclick="closeModal('modalAddProduk')">&times;</button>
        </div>
        <form action="<?= base_url('produk/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Kode Barcode *</label>
                        <input type="text" name="barcode" class="form-control" placeholder="Contoh: 899123456789" required autofocus>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="kategori_id" class="form-select">
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($kategori as $kat): ?>
                                <option value="<?= $kat['id'] ?>"><?= esc($kat['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Produk *</label>
                    <input type="text" name="nama_produk" class="form-control" placeholder="Nama lengkap produk retail" required>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Harga Beli / HPP (Rp) *</label>
                        <input type="number" id="addHargaBeli" name="harga_beli" class="form-control" placeholder="0" min="0" step="100" required oninput="calcMarginAdd()">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga Jual (Rp) *</label>
                        <input type="number" id="addHargaJual" name="harga_jual" class="form-control" placeholder="0" min="0" step="100" required oninput="calcMarginAdd()">
                    </div>
                </div>

                <div style="margin-bottom: 16px; padding: 10px; background: #ECFDF5; border-radius: 8px; font-size: 12px; color: #064E3B;">
                    Estimasi Laba Kotor per Item: <b id="addMarginInfo">Rp 0 (0%)</b>
                </div>

                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label">Stok Awal Fisik *</label>
                        <input type="number" name="stok" class="form-control" value="0" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Batas Min. Stok *</label>
                        <input type="number" name="min_stock" class="form-control" value="5" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Satuan</label>
                        <input type="text" name="satuan" class="form-control" value="pcs" placeholder="pcs/btl/bks">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Deskripsi / Keterangan</label>
                    <textarea name="deskripsi" class="form-control" rows="2" placeholder="Catatan tambahan produk"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="antigravity-btn-outline" onclick="closeModal('modalAddProduk')">Batal</button>
                <button type="submit" class="antigravity-btn"><i class="fa-solid fa-save"></i> Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ubah Produk -->
<div id="modalEditProduk" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fa-solid fa-pen-to-square" style="color: var(--accent-green);"></i> Ubah Data Produk</h3>
            <button type="button" class="modal-close" onclick="closeModal('modalEditProduk')">&times;</button>
        </div>
        <form id="formEditProduk" action="" method="POST">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Kode Barcode *</label>
                        <input type="text" id="editBarcode" name="barcode" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select id="editKategori" name="kategori_id" class="form-select">
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($kategori as $kat): ?>
                                <option value="<?= $kat['id'] ?>"><?= esc($kat['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Produk *</label>
                    <input type="text" id="editNama" name="nama_produk" class="form-control" required>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Harga Beli / HPP (Rp) *</label>
                        <input type="number" id="editHargaBeli" name="harga_beli" class="form-control" min="0" step="100" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga Jual (Rp) *</label>
                        <input type="number" id="editHargaJual" name="harga_jual" class="form-control" min="0" step="100" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Batas Min. Stok *</label>
                        <input type="number" id="editMinStock" name="min_stock" class="form-control" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Satuan</label>
                        <input type="text" id="editSatuan" name="satuan" class="form-control" required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Deskripsi / Keterangan</label>
                    <textarea id="editDeskripsi" name="deskripsi" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="antigravity-btn-outline" onclick="closeModal('modalEditProduk')">Batal</button>
                <button type="submit" class="antigravity-btn"><i class="fa-solid fa-save"></i> Perbarui Produk</button>
            </div>
        </form>
    </div>
</div>

<script>
    function calcMarginAdd() {
        const beli = parseFloat(document.getElementById('addHargaBeli').value) || 0;
        const jual = parseFloat(document.getElementById('addHargaJual').value) || 0;
        const margin = jual - beli;
        const pct = (jual > 0) ? ((margin / jual) * 100).toFixed(1) : 0;
        document.getElementById('addMarginInfo').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(margin) + ' (' + pct + '%)';
    }

    function editProduk(p) {
        document.getElementById('formEditProduk').action = '<?= base_url('produk/update') ?>/' + p.id;
        document.getElementById('editBarcode').value = p.barcode;
        document.getElementById('editNama').value = p.nama_produk;
        document.getElementById('editKategori').value = p.kategori_id || '';
        document.getElementById('editHargaBeli').value = p.harga_beli;
        document.getElementById('editHargaJual').value = p.harga_jual;
        document.getElementById('editMinStock').value = p.min_stock;
        document.getElementById('editSatuan').value = p.satuan;
        document.getElementById('editDeskripsi').value = p.deskripsi || '';
        openModal('modalEditProduk');
    }
</script>

<?= $this->endSection() ?>

