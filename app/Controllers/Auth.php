<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Tampilkan formulir login
     */
    public function login()
    {
        // Jika sudah login, redirect sesuai peran
        if (session()->get('logged_in')) {
            return session()->get('role') === 'kasir' 
                ? redirect()->to('/pos') 
                : redirect()->to('/dashboard');
        }

        return view('auth/login', [
            'title' => 'Login — Antigravity Retail POS',
        ]);
    }

    /**
     * Proses autentikasi kredensial login (FR-1.1 & FR-1.3)
     */
    public function attemptLogin()
    {
        $session = session();
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        if (empty($username) || empty($password)) {
            $session->setFlashdata('error', 'Username dan password wajib diisi.');
            return redirect()->back()->withInput();
        }

        $user = $this->userModel->findByUsername($username);

        if (!$user) {
            $session->setFlashdata('error', 'Kredensial username atau password tidak valid.');
            return redirect()->back()->withInput();
        }

        // Cek status aktif
        if ((int) $user['is_active'] !== 1) {
            $session->setFlashdata('error', 'Akun Anda telah dinonaktifkan oleh Administrator.');
            return redirect()->back()->withInput();
        }

        // Proteksi Brute-force: Cek status terkunci
        if (!empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
            $remaining = ceil((strtotime($user['locked_until']) - time()) / 60);
            $session->setFlashdata('error', "Terlalu banyak percobaan gagal. Akun dikunci sementara selama {$remaining} menit.");
            return redirect()->back();
        }

        // Verifikasi password hash (PASSWORD_BCRYPT)
        if (!password_verify($password, $user['password'])) {
            $attempts = $this->userModel->recordFailedLogin((int) $user['id'], (int) $user['failed_login_attempts']);
            $sisa = 5 - $attempts;

            if ($sisa > 0) {
                $session->setFlashdata('error', "Password salah. Sisa kesempatan login: {$sisa} kali sebelum akun terkunci.");
            } else {
                $session->setFlashdata('error', 'Percobaan gagal mencapai batas maksimum (5x). Akun dikunci selama 15 menit.');
            }

            return redirect()->back()->withInput();
        }

        // Reset status percobaan gagal setelah autentikasi sukses
        $this->userModel->resetFailedLogin((int) $user['id']);

        // Simpan data ke session
        $session->set([
            'user_id'      => (int) $user['id'],
            'username'     => $user['username'],
            'nama_lengkap' => $user['nama_lengkap'],
            'role'         => $user['role'],
            'logged_in'    => true,
        ]);

        $session->setFlashdata('success', 'Selamat datang kembali, ' . esc($user['nama_lengkap']) . '!');

        // FR-1.3: Redirect otomatis berdasarkan peran
        if ($user['role'] === 'kasir') {
            return redirect()->to('/pos');
        }

        return redirect()->to('/dashboard');
    }

    /**
     * Proses logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}

