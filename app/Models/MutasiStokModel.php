<?php

namespace App\Models;

use CodeIgniter\Model;

class MutasiStokModel extends Model
{
    protected $table            = 'mutasi_stok';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'produk_id',
        'user_id',
        'tipe',
        'kuantitas',
        'stok_sebelum',
        'stok_sesudah',
        'nomor_faktur',
        'keterangan',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Catat log mutasi stok
     */
    public function logMutation(array $data)
    {
        if (empty($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        return $this->insert($data);
    }

    /**
     * Ambil riwayat mutasi dengan relasi produk dan user
     */
    public function getDetailedLogs(?int $produkId = null, ?string $tipe = null, ?string $startDate = null, ?string $endDate = null)
    {
        $builder = $this->select('mutasi_stok.*, produk.nama_produk, produk.barcode, produk.satuan, users.nama_lengkap as nama_user')
            ->join('produk', 'produk.id = mutasi_stok.produk_id', 'inner')
            ->join('users', 'users.id = mutasi_stok.user_id', 'left');

        if ($produkId) {
            $builder->where('mutasi_stok.produk_id', $produkId);
        }

        if ($tipe) {
            $builder->where('mutasi_stok.tipe', $tipe);
        }

        if ($startDate) {
            $builder->where('mutasi_stok.created_at >=', $startDate . ' 00:00:00');
        }

        if ($endDate) {
            $builder->where('mutasi_stok.created_at <=', $endDate . ' 23:59:59');
        }

        return $builder->orderBy('mutasi_stok.created_at', 'DESC')->findAll();
    }
}

