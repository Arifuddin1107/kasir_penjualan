<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Antigravity Retail POS') ?></title>
    <!-- Meta CSRF -->
    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-hash" content="<?= csrf_hash() ?>">

    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Feather Icons / FontAwesome via CDN for crisp UI icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Chart.js for Dashboard Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --bg-page: #FAFCFA;
            --bg-card: #FFFFFF;
            --accent-green: #059669;
            --accent-green-hover: #047857;
            --accent-green-light: #ECFDF5;
            --accent-green-border: #A7F3D0;
            --text-primary: #064E3B;
            --text-muted: #6B7280;
            --border-color: #E5E7EB;
            --danger-bg: #FEF2F2;
            --danger-border: #FECACA;
            --danger-text: #DC2626;
            --warning-bg: #FFFBEB;
            --warning-border: #FDE68A;
            --warning-text: #D97706;
            --blue-bg: #EFF6FF;
            --blue-border: #BFDBFE;
            --blue-text: #2563EB;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-page);
            color: #1F2937;
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Navbar */
        .top-navbar {
            background-color: #FFFFFF;
            border-bottom: 1px solid var(--accent-green-border);
            position: sticky;
            top: 0;
            z-index: 50;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            box-shadow: 0 1px 3px rgba(5, 150, 105, 0.04);
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--text-primary);
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #059669 0%, #10B981 100%);
            color: #FFFFFF;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 4px 10px rgba(5, 150, 105, 0.2);
        }

        .brand-title {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-primary);
        }

        .brand-badge {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--accent-green);
            background: var(--accent-green-light);
            border: 1px solid var(--accent-green-border);
            padding: 2px 7px;
            border-radius: 9999px;
            margin-left: 6px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-muted);
            transition: all 0.15s ease;
        }

        .nav-link:hover {
            color: var(--accent-green);
            background-color: var(--accent-green-light);
        }

        .nav-link.active {
            color: #FFFFFF;
            background-color: var(--accent-green);
            box-shadow: 0 2px 6px rgba(5, 150, 105, 0.25);
        }

        .user-nav {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .role-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11.5px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .role-pill.admin {
            background-color: #ECFDF5;
            color: #059669;
            border: 1px solid #A7F3D0;
        }

        .role-pill.kasir {
            background-color: #EFF6FF;
            color: #2563EB;
            border: 1px solid #BFDBFE;
        }

        .btn-logout {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            font-size: 12.5px;
            font-weight: 600;
            color: #DC2626;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-logout:hover {
            background: #FEE2E2;
            color: #B91C1C;
        }

        /* Container & Cards */
        .app-container {
            flex: 1;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            padding: 24px 24px 48px 24px;
        }

        .antigravity-card {
            background: var(--bg-card);
            border: 1px solid var(--accent-green-border);
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 4px 20px -2px rgba(5, 150, 105, 0.04);
            margin-bottom: 24px;
        }

        .antigravity-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 16px;
            margin-bottom: 16px;
            border-bottom: 1px solid #F3F4F6;
        }

        .card-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Buttons */
        .antigravity-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: var(--accent-green);
            color: #FFFFFF;
            border: none;
            padding: 9px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13.5px;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .antigravity-btn:hover {
            background: var(--accent-green-hover);
            box-shadow: 0 2px 8px rgba(5, 150, 105, 0.3);
        }

        .antigravity-btn-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #FFFFFF;
            color: var(--accent-green);
            border: 1px solid var(--accent-green-border);
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .antigravity-btn-outline:hover {
            background: var(--accent-green-light);
            border-color: var(--accent-green);
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 6px;
        }

        .btn-danger {
            background: #DC2626;
            color: white;
            border: none;
        }
        .btn-danger:hover {
            background: #B91C1C;
        }

        /* Alert Notifications */
        .alert {
            padding: 12px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13.5px;
            font-weight: 500;
        }

        .alert-success {
            background-color: var(--accent-green-light);
            border: 1px solid var(--accent-green-border);
            color: var(--text-primary);
        }

        .alert-error {
            background-color: var(--danger-bg);
            border: 1px solid var(--danger-border);
            color: var(--danger-text);
        }

        .alert-warning {
            background-color: var(--warning-bg);
            border: 1px solid var(--warning-border);
            color: var(--warning-text);
        }

        /* Tables */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .antigravity-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 13px;
        }

        .antigravity-table th {
            background-color: #F9FAFB;
            color: var(--text-muted);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.05em;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
            text-align: left;
        }

        .antigravity-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #F3F4F6;
            vertical-align: middle;
            color: #374151;
        }

        .antigravity-table tbody tr:hover {
            background-color: #FAFCFA;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11.5px;
            font-weight: 700;
        }

        .badge-success {
            background-color: #ECFDF5;
            color: #059669;
            border: 1px solid #A7F3D0;
        }

        .badge-danger {
            background-color: #FEF2F2;
            color: #DC2626;
            border: 1px solid #FECACA;
        }

        .badge-warning {
            background-color: #FFFBEB;
            color: #D97706;
            border: 1px solid #FDE68A;
        }

        .badge-info {
            background-color: #EFF6FF;
            color: #2563EB;
            border: 1px solid #BFDBFE;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-control, .form-select {
            width: 100%;
            padding: 9px 13px;
            font-size: 13.5px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            background-color: #FFFFFF;
            color: #1F2937;
            transition: all 0.15s ease;
            font-family: inherit;
        }

        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: var(--accent-green);
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.15);
        }

        /* Grid */
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }

        @media (max-width: 900px) {
            .grid-4 { grid-template-columns: repeat(2, 1fr); }
            .grid-3 { grid-template-columns: 1fr; }
            .nav-links { display: none; }
        }

        /* Modals */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(17, 24, 39, 0.45);
            backdrop-filter: blur(2px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 16px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-card {
            background-color: #FFFFFF;
            border-radius: 14px;
            max-width: 580px;
            width: 100%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--accent-green-border);
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 22px;
            border-bottom: 1px solid #F3F4F6;
        }

        .modal-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: var(--text-muted);
        }

        .modal-body {
            padding: 20px 22px;
        }

        .modal-footer {
            padding: 14px 22px;
            background-color: #F9FAFB;
            border-top: 1px solid #F3F4F6;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-radius: 0 0 14px 14px;
        }
    </style>
</head>
<body>

    <!-- Header Navigation Bar -->
    <header class="top-navbar">
        <a href="<?= base_url(session()->get('role') === 'kasir' ? 'pos' : 'dashboard') ?>" class="brand-logo">
            <div class="brand-icon">
                <i class="fa-solid fa-feather-pointed"></i>
            </div>
            <div>
                <span class="brand-title">Antigravity Retail</span>
                <span class="brand-badge">CI4 POS</span>
            </div>
        </a>

        <!-- Menu Berjenjang Sesuai Role (FR-1.2) -->
        <nav class="nav-links">
            <?php $uri = service('uri')->getPath(); ?>
            
            <?php if (session()->get('role') === 'admin'): ?>
                <a href="<?= base_url('dashboard') ?>" class="nav-link <?= str_contains($uri, 'dashboard') ? 'active' : '' ?>">
                    <i class="fa-solid fa-chart-pie"></i> Dashboard
                </a>
                <a href="<?= base_url('produk') ?>" class="nav-link <?= str_contains($uri, 'produk') ? 'active' : '' ?>">
                    <i class="fa-solid fa-boxes-stacked"></i> Produk
                </a>
                <a href="<?= base_url('kategori') ?>" class="nav-link <?= str_contains($uri, 'kategori') ? 'active' : '' ?>">
                    <i class="fa-solid fa-tags"></i> Kategori
                </a>
                <a href="<?= base_url('mutasi') ?>" class="nav-link <?= str_contains($uri, 'mutasi') ? 'active' : '' ?>">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i> Mutasi Stok
                </a>
                <a href="<?= base_url('laporan') ?>" class="nav-link <?= str_contains($uri, 'laporan') ? 'active' : '' ?>">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Laporan
                </a>
                <a href="<?= base_url('pengguna') ?>" class="nav-link <?= str_contains($uri, 'pengguna') ? 'active' : '' ?>">
                    <i class="fa-solid fa-users-gear"></i> Pengguna
                </a>
                <a href="<?= base_url('pengaturan') ?>" class="nav-link <?= str_contains($uri, 'pengaturan') ? 'active' : '' ?>">
                    <i class="fa-solid fa-sliders"></i> Pengaturan
                </a>
            <?php endif; ?>

            <!-- Kasir & Admin sama-sama memiliki akses ke POS -->
            <a href="<?= base_url('pos') ?>" class="nav-link <?= str_contains($uri, 'pos') ? 'active' : '' ?>">
                <i class="fa-solid fa-cash-register"></i> Kasir POS
            </a>
        </nav>

        <!-- User Profile & Logout -->
        <div class="user-nav">
            <span class="role-pill <?= esc(session()->get('role')) ?>">
                <i class="fa-solid fa-user-circle"></i>
                <?= esc(session()->get('nama_lengkap') ?? session()->get('username')) ?> (<?= esc(session()->get('role')) ?>)
            </span>
            <a href="<?= base_url('logout') ?>" class="btn-logout" title="Keluar">
                <i class="fa-solid fa-right-from-bracket"></i> Keluar
            </a>
        </div>
    </header>

    <!-- Main Container Content -->
    <main class="app-container">
        <!-- Flash Message Banners -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <div><?= session()->getFlashdata('success') ?></div>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div><?= session()->getFlashdata('error') ?></div>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>

    <script>
        // Global Modal helper
        function openModal(id) {
            const m = document.getElementById(id);
            if (m) m.classList.add('active');
        }
        function closeModal(id) {
            const m = document.getElementById(id);
            if (m) m.classList.remove('active');
        }

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.active').forEach(modal => {
                    modal.classList.remove('active');
                });
            }
        });
    </script>
</body>
</html>

