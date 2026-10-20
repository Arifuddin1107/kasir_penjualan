<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleAuthFilter implements FilterInterface
{
    /**
     * Verifikasi autentikasi dan otorisasi peran
     *
     * @param array|null $arguments Daftar role yang diizinkan, misal: ['admin']
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // 1. Cek apakah user sudah login
        if (!$session->get('logged_in')) {
            if ($request->isAJAX()) {
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON([
                        'status'  => 'unauthenticated',
                        'message' => 'Sesi login telah berakhir. Silakan login kembali.',
                    ]);
            }

            $session->setFlashdata('error', 'Silakan login terlebih dahulu untuk mengakses sistem.');
            return redirect()->to('/login');
        }

        // 2. Cek otorisasi peran jika ada argumen role yang ditentukan
        $userRole = $session->get('role');

        if (!empty($arguments)) {
            if (!in_array($userRole, $arguments, true)) {
                if ($request->isAJAX()) {
                    return service('response')
                        ->setStatusCode(403)
                        ->setJSON([
                            'status'  => 'forbidden',
                            'message' => 'Akses ditolak: Anda tidak memiliki hak otorisasi untuk tindakan ini.',
                        ]);
                }

                $session->setFlashdata('error', 'Akses dibatasi! Kasir tidak diizinkan mengakses menu finansial atau master data admin.');

                // Kasir diarahkan otomatis ke POS
                if ($userRole === 'kasir') {
                    return redirect()->to('/pos');
                }

                return redirect()->to('/dashboard');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after
    }
}

