<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'username',
        'nama_lengkap',
        'password',
        'role',
        'is_active',
        'failed_login_attempts',
        'locked_until',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Cari user berdasarkan username
     */
    public function findByUsername(string $username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Catat gagal login untuk proteksi brute force
     */
    public function recordFailedLogin(int $userId, int $currentAttempts)
    {
        $newAttempts = $currentAttempts + 1;
        $data = ['failed_login_attempts' => $newAttempts];

        // Kunci akun selama 15 menit jika gagal 5 kali berturut-turut
        if ($newAttempts >= 5) {
            $data['locked_until'] = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        }

        $this->update($userId, $data);
        return $newAttempts;
    }

    /**
     * Reset percobaan gagal setelah login berhasil
     */
    public function resetFailedLogin(int $userId)
    {
        $this->update($userId, [
            'failed_login_attempts' => 0,
            'locked_until'          => null,
        ]);
    }
}

