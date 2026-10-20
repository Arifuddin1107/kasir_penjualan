<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Login — Antigravity Retail POS') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            background-color: var(--bg-card);
            border: 1px solid var(--accent-green-border);
            border-radius: 16px;
            padding: 36px 32px;
            max-width: 440px;
            width: 100%;
            box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.08);
        }

        .login-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .login-icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #059669 0%, #10B981 100%);
            color: #FFFFFF;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 14px rgba(5, 150, 105, 0.25);
            margin-bottom: 14px;
        }

        .login-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.02em;
        }

        .login-subtitle {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .alert-error {
            background-color: #FEF2F2;
            border: 1px solid #FECACA;
            color: #DC2626;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background-color: #ECFDF5;
            border: 1px solid #A7F3D0;
            color: #059669;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 11px 14px 11px 40px;
            font-size: 14px;
            border: 1px solid #D1D5DB;
            border-radius: 10px;
            background-color: #FFFFFF;
            transition: all 0.15s ease;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-green);
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.15);
        }

        .btn-submit {
            width: 100%;
            background-color: var(--accent-green);
            color: #FFFFFF;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s ease;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background-color: var(--accent-green-hover);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }

        /* Demo credentials pills */
        .demo-box {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px dashed #E5E7EB;
        }

        .demo-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 10px;
            text-align: center;
        }

        .demo-pills {
            display: flex;
            gap: 10px;
        }

        .demo-pill {
            flex: 1;
            padding: 8px;
            border-radius: 8px;
            font-size: 12px;
            cursor: pointer;
            border: 1px solid var(--accent-green-border);
            background: var(--accent-green-light);
            color: var(--text-primary);
            text-align: center;
            transition: all 0.15s ease;
        }

        .demo-pill:hover {
            background: #D1FAE5;
        }

        .demo-pill b {
            display: block;
            font-size: 12.5px;
        }

        .demo-pill span {
            font-size: 11px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <div class="login-icon">
                <i class="fa-solid fa-feather-pointed"></i>
            </div>
            <h1 class="login-title">Sistem POS & Inventaris</h1>
            <p class="login-subtitle">Masuk untuk memulai sesi transaksi & operasional</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert-error">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div><?= session()->getFlashdata('error') ?></div>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <div><?= session()->getFlashdata('success') ?></div>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('login/attempt') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <div class="input-group">
                    <i class="fa-regular fa-user input-icon"></i>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan username" value="<?= old('username') ?>" required autofocus autocomplete="username">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-group">
                    <i class="fa-solid fa-lock input-icon"></i>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan password" required autocomplete="current-password">
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk ke Sistem
            </button>
        </form>

        <div class="demo-box">
            <div class="demo-title">Akun Uji Coba Cepat (Klik untuk mengisi)</div>
            <div class="demo-pills">
                <button type="button" class="demo-pill" onclick="fillCred('admin', 'admin123')">
                    <b>Admin / Owner</b>
                    <span>admin / admin123</span>
                </button>
                <button type="button" class="demo-pill" onclick="fillCred('kasir', 'kasir123')">
                    <b>Kasir Gardu</b>
                    <span>kasir / kasir123</span>
                </button>
            </div>
        </div>
    </div>

       <script>
        function fillCred(user, pass) {
            document.getElementById('username').value = user;
            document.getElementById('password').value = pass;
        }
    </script>
</body>
</html>

