<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table            = 'kategori';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_kategori'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil kategori beserta jumlah produk
     */
    public function getWithProductCount()
    {
        return $this->select('kategori.*, COUNT(produk.id) as total_produk')
            ->join('produk', 'produk.kategori_id = kategori.id', 'left')
            ->groupBy('kategori.id')
            ->orderBy('kategori.nama_kategori', 'ASC')
            ->findAll();
    }
}

