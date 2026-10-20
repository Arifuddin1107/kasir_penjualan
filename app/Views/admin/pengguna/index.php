<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h1 style="font-size: 22px; font-weight: 800; color: var(--text-primary); letter-spacing: -0.02em;">Manajemen Pengguna & Otorisasi Kredensial</h1>
        <p style="font-size: 13.5px; color: var(--text-muted); margin-top: 2px;">Kelola akun Admin dan Kasir serta pantau keamanan proteksi brute-force.</p>
    </div>
    <div>
        <button type="button" class="antigravity-btn" onclick="openModal('modalAddUser')">
            <i class="fa-solid fa-user-plus"></i> Tambah Pengguna Baru
        </button>
    </div>
</div>

<div class="antigravity-card">
    <div class="table-responsive">
        <table class="antigravity-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Peran (Role)</th>
                    <th>Status Akun</th>
                    <th>Proteksi Brute-force</th>
                    <th>Terdaftar Sejak</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($users as $u): ?>
                    <?php 
                        $isLocked = (!empty($u['locked_until']) && strtotime($u['locked_until']) > time());
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <strong><?= esc($u['nama_lengkap']) ?></strong>
                            <?php if ($u['id'] == session()->get('user_id')): ?>
                                <span class="brand-badge" style="margin-left: 4px;">Akun Anda</span>
                            <?php endif; ?>
                        </td>
                        <td><code><?= esc($u['username']) ?></code></td>
                        <td>
                            <span class="role-pill <?= esc($u['role']) ?>">
                                <i class="fa-solid <?= $u['role'] === 'admin' ? 'fa-user-shield' : 'fa-cash-register' ?>"></i>
                                <?= strtoupper(esc($u['role'])) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($u['is_active'] == 1): ?>
                                <span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-danger"><i class="fa-solid fa-ban"></i> Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($isLocked): ?>
                                <span class="badge badge-danger" title="Terkunci hingga <?= $u['locked_until'] ?>">
                                    <i class="fa-solid fa-lock"></i> Terkunci (5x Gagal)
                                </span>
                            <?php else: ?>
                                <span class="badge badge-success">
                                    <i class="fa-solid fa-shield-halved"></i> Normal (<?= $u['failed_login_attempts'] ?> fail)
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($u['created_at'] ?? 'now')) ?></td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 6px;">
                                <?php if ($isLocked): ?>
                                    <form action="<?= base_url('pengguna/unlock/' . $u['id']) ?>" method="POST" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="antigravity-btn-outline btn-sm" style="color: #059669; border-color: #A7F3D0;" title="Buka Kunci Akun">
                                            <i class="fa-solid fa-lock-open"></i> Reset
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <button type="button" class="antigravity-btn-outline btn-sm" onclick='editUser(<?= json_encode($u) ?>)'>
                                    <i class="fa-solid fa-pen-to-square"></i> Ubah
                                </button>

                                <?php if ($u['id'] != session()->get('user_id')): ?>
                                    <a href="<?= base_url('pengguna/delete/' . $u['id']) ?>" class="antigravity-btn btn-sm btn-danger" onclick="return confirm('Hapus pengguna ini?')" title="Hapus Akun">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah User -->
<div id="modalAddUser" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fa-solid fa-user-plus" style="color: var(--accent-green);"></i> Tambah Pengguna Baru</h3>
            <button type="button" class="modal-close" onclick="closeModal('modalAddUser')">&times;</button>
        </div>
        <form action="<?= base_url('pengguna/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Username *</label>
                        <input type="text" name="username" class="form-control" placeholder="Contoh: kasir02" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Peran Akses (Role) *</label>
                        <select name="role" class="form-select" required>
                            <option value="kasir">Kasir (Hanya Layar Transaksi POS)</option>
                            <option value="admin">Admin / Owner (Akses Penuh Finansial)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama lengkap petugas" required>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="antigravity-btn-outline" onclick="closeModal('modalAddUser')">Batal</button>
                <button type="submit" class="antigravity-btn">Buat Pengguna</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ubah User -->
<div id="modalEditUser" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title"><i class="fa-solid fa-user-pen" style="color: var(--accent-green);"></i> Ubah Data Pengguna</h3>
            <button type="button" class="modal-close" onclick="closeModal('modalEditUser')">&times;</button>
        </div>
        <form id="formEditUser" action="" method="POST">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Username (Read-only)</label>
                        <input type="text" id="editUsername" class="form-control" disabled style="background: #F3F4F6;">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Peran Akses (Role) *</label>
                        <select id="editRole" name="role" class="form-select" required>
                            <option value="kasir">Kasir</option>
                            <option value="admin">Admin / Owner</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" id="editNamaLengkap" name="nama_lengkap" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Ganti Password (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" class="form-control" placeholder="Ketik password baru jika ingin mereset">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" id="editIsActive" name="is_active" value="1" style="width: 18px; height: 18px; accent-color: var(--accent-green);">
                        <span>Status Akun Aktif</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="antigravity-btn-outline" onclick="closeModal('modalEditUser')">Batal</button>
                <button type="submit" class="antigravity-btn">Perbarui Akun</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editUser(u) {
        document.getElementById('formEditUser').action = '<?= base_url('pengguna/update') ?>/' + u.id;
        document.getElementById('editUsername').value = u.username;
        document.getElementById('editNamaLengkap').value = u.nama_lengkap;
        document.getElementById('editRole').value = u.role;
        document.getElementById('editIsActive').checked = (parseInt(u.is_active) === 1);
        openModal('modalEditUser');
    }
</script>

<?= $this->endSection() ?>

