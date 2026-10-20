<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h1 style="font-size: 22px; font-weight: 800; color: var(--text-primary); letter-spacing: -0.02em;">Manajemen Kategori Produk</h1>
        <p style="font-size: 13.5px; color: var(--text-muted); margin-top: 2px;">Klasifikasikan produk retail untuk navigasi cepat di antarmuka kasir.</p>
    </div>
    <div>
        <button type="button" class="antigravity-btn" onclick="openModal('modalAddKategori')">
            <i class="fa-solid fa-plus"></i> Tambah Kategori
        </button>
    </div>
</div>

<div class="antigravity-card">
    <div class="table-responsive">
        <table class="antigravity-table">
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Nama Kategori</th>
                    <th>Jumlah Produk Terdaftar</th>
                    <th>Tanggal Dibuat</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($kategori)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px;">
                            Belum ada kategori yang dibuat.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($kategori as $kat): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <strong><?= esc($kat['nama_kategori']) ?></strong>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    <i class="fa-solid fa-boxes-stacked"></i> <?= $kat['total_produk'] ?> Produk
                                </span>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($kat['created_at'] ?? 'now')) ?></td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <button type="button" class="antigravity-btn-outline btn-sm" onclick='editKategori(<?= json_encode($kat) ?>)'>
                                        <i class="fa-solid fa-pen-to-square"></i> Ubah
                                    </button>
                                    <a href="<?= base_url('kategori/delete/' . $kat['id']) ?>" class="antigravity-btn btn-sm btn-danger" onclick="return confirm('Hapus kategori ini? Jika memiliki relasi produk aktif, penghapusan akan dicegah oleh database.')">
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

<!-- Modal Tambah Kategori -->
<div id="modalAddKategori" class="modal-overlay">
    <div class="modal-card" style="max-width: 440px;">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fa-solid fa-tags" style="color: var(--accent-green);"></i> Tambah Kategori</h3>
            <button type="button" class="modal-close" onclick="closeModal('modalAddKategori')">&times;</button>
        </div>
        <form action="<?= base_url('kategori/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Nama Kategori *</label>
                    <input type="text" name="nama_kategori" class="form-control" placeholder="Misal: Minuman Dingin, Snack" required autofocus>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="antigravity-btn-outline" onclick="closeModal('modalAddKategori')">Batal</button>
                <button type="submit" class="antigravity-btn">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ubah Kategori -->
<div id="modalEditKategori" class="modal-overlay">
    <div class="modal-card" style="max-width: 440px;">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fa-solid fa-pen-to-square" style="color: var(--accent-green);"></i> Ubah Kategori</h3>
            <button type="button" class="modal-close" onclick="closeModal('modalEditKategori')">&times;</button>
        </div>
        <form id="formEditKategori" action="" method="POST">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Nama Kategori *</label>
                    <input type="text" id="editNamaKategori" name="nama_kategori" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="antigravity-btn-outline" onclick="closeModal('modalEditKategori')">Batal</button>
                <button type="submit" class="antigravity-btn">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editKategori(k) {
        document.getElementById('formEditKategori').action = '<?= base_url('kategori/update') ?>/' + k.id;
        document.getElementById('editNamaKategori').value = k.nama_kategori;
        openModal('modalEditKategori');
    }
</script>

<?= $this->endSection() ?>

