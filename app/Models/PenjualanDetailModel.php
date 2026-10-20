<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanDetailModel extends Model
{
    protected $table            = 'penjualan_detail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'penjualan_id',
        'produk_id',
        'barcode',
        'nama_produk',
        'harga_beli',
        'harga_jual',
        'qty',
        'subtotal_omzet',
        'subtotal_hpp',
        'subtotal_laba',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Ambil rincian item berdasarkan id transaksi penjualan
     */
    public function getItemsByPenjualanId(int $penjualanId)
    {
        return $this->where('penjualan_id', $penjualanId)
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}

