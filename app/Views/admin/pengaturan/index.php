<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h1 style="font-size: 22px; font-weight: 800; color: var(--text-primary); letter-spacing: -0.02em;">Pengaturan Profil Toko & Format Struk</h1>
        <p style="font-size: 13.5px; color: var(--text-muted); margin-top: 2px;">Konfigurasi identitas gerai dan preferensi printer thermal POS (58mm / 80mm).</p>
    </div>
</div>

<div style="max-width: 680px;">
    <div class="antigravity-card">
        <form action="<?= base_url('pengaturan/update') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="antigravity-card-header">
                <h2 class="card-title"><i class="fa-solid fa-store" style="color: var(--accent-green);"></i> Identitas Toko Retail</h2>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Toko *</label>
                <input type="text" name="nama_toko" class="form-control" value="<?= esc($settings['nama_toko']) ?>" required>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="no_telp" class="form-control" value="<?= esc($settings['no_telp']) ?>" placeholder="0812-3456-7890">
                </div>

                <div class="form-group">
                    <label class="form-label">Ukuran Kertas Struk Thermal (FR-4.1)</label>
                    <select name="paper_size" class="form-select">
                        <option value="58mm" <?= ($settings['paper_size'] === '58mm') ? 'selected' : '' ?>>58 mm (Printer Mini Thermal Standar)</option>
                        <option value="80mm" <?= ($settings['paper_size'] === '80mm') ? 'selected' : '' ?>>80 mm (Printer Thermal Kasir Lebar)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Lengkap Gerai</label>
                <textarea name="alamat" class="form-control" rows="2"><?= esc($settings['alamat']) ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan Kaki Struk (Footer Struk)</label>
                <textarea name="footer_struk" class="form-control" rows="2" placeholder="Pesan terima kasih atau syarat ketentuan singkat"><?= esc($settings['footer_struk']) ?></textarea>
            </div>

            <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #F3F4F6; display: flex; justify-content: flex-end;">
                <button type="submit" class="antigravity-btn">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Konfigurasi
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

