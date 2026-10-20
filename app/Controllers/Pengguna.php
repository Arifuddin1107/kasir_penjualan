<?php

namespace App\Controllers;

use App\Models\UserModel;

class Pengguna extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $users = $this->userModel->orderBy('role', 'ASC')->orderBy('nama_lengkap', 'ASC')->findAll();

        $data = [
            'title' => 'Manajemen Pengguna & Hak Akses — Antigravity Retail',
            'users' => $users,
        ];

        return view('admin/pengguna/index', $data);
    }

    public function store()
    {
        $username    = trim((string) $this->request->getPost('username'));
        $namaLengkap = trim((string) $this->request->getPost('nama_lengkap'));
        $password    = (string) $this->request->getPost('password');
        $role        = (string) $this->request->getPost('role');

        if (empty($username) || empty($namaLengkap) || empty($password) || !in_array($role, ['admin', 'kasir'], true)) {
            return redirect()->back()->withInput()->with('error', 'Semua kolom pengguna wajib diisi dengan benar.');
        }

        if ($this->userModel->findByUsername($username)) {
            return redirect()->back()->withInput()->with('error', "Username '{$username}' sudah digunakan.");
        }

        $this->userModel->insert([
            'username'              => $username,
            'nama_lengkap'          => $namaLengkap,
            'password'              => password_hash($password, PASSWORD_BCRYPT),
            'role'                  => $role,
            'is_active'             => 1,
            'failed_login_attempts' => 0,
        ]);

        return redirect()->to('/pengguna')->with('success', "Pengguna '{$namaLengkap}' ({$role}) berhasil dibuat.");
    }

    public function update($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/pengguna')->with('error', 'Pengguna tidak ditemukan.');
        }

        $namaLengkap = trim((string) $this->request->getPost('nama_lengkap'));
        $password    = (string) $this->request->getPost('password');
        $role        = (string) $this->request->getPost('role');
        $isActive    = $this->request->getPost('is_active') ? 1 : 0;

        $updateData = [
            'nama_lengkap' => $namaLengkap,
            'role'         => $role,
            'is_active'    => $isActive,
        ];

        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->userModel->update($id, $updateData);

        return redirect()->to('/pengguna')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function unlock($id)
    {
        $this->userModel->resetFailedLogin((int) $id);
        return redirect()->to('/pengguna')->with('success', 'Kunci proteksi brute-force berhasil di-reset.');
    }

    public function delete($id)
    {
        // Cegah menghapus akun sendiri
        if ((int) $id === (int) session()->get('user_id')) {
            return redirect()->to('/pengguna')->with('error', 'Anda tidak dapat menghapus akun yang sedang Anda gunakan.');
        }

        try {
            $this->userModel->delete($id);
            return redirect()->to('/pengguna')->with('success', 'Pengguna berhasil dihapus.');
        } catch (\Throwable $e) {
            return redirect()->to('/pengguna')->with('error', 'Pengguna tidak dapat dihapus karena tercatat pada riwayat transaksi kasir (Foreign Key RESTRICT).');
        }
    }
}

