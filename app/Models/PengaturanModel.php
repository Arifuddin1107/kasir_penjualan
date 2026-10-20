<?php

namespace App\Models;

use CodeIgniter\Model;

class PengaturanModel extends Model
{
    protected $table            = 'pengaturan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_toko',
        'alamat',
        'no_telp',
        'footer_struk',
        'paper_size',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = '';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil data pengaturan toko singleton (record pertama)
     */
    public function getSettings()
    {
        $setting = $this->first();
        if (!$setting) {
            $default = [
                'nama_toko'    => 'Antigravity Retail Mart',
                'alamat'       => 'Jl. Emerald Avenue No. 108',
                'no_telp'      => '0812-3456-7890',
                'footer_struk' => 'Terima kasih atas kunjungan Anda',
                'paper_size'   => '58mm',
            ];
            $this->insert($default);
            return $this->first();
        }
        return $setting;
    }
}

