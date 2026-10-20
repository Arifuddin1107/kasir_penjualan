<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table            = 'produk';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kategori_id',
        'barcode',
        'nama_produk',
        'harga_beli',
        'harga_jual',
        'stok',
        'min_stock',
        'satuan',
        'deskripsi',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil produk beserta nama kategori
     */
    public function getWithKategori($id = null)
    {
        $builder = $this->select('produk.*, kategori.nama_kategori')
            ->join('kategori', 'kategori.id = produk.kategori_id', 'left');

        if ($id !== null) {
            return $builder->where('produk.id', $id)->first();
        }

        return $builder->orderBy('produk.nama_produk', 'ASC')->findAll();
    }

    /**
     * Cari produk berdasarkan Barcode atau Nama Produk (untuk POS Search & Scanner)
     */
    public function searchForPos(string $keyword)
    {
        return $this->select('id, barcode, nama_produk, harga_beli, harga_jual, stok, min_stock, satuan')
            ->groupStart()
                ->where('barcode', $keyword)
                ->orLike('nama_produk', $keyword)
            ->groupEnd()
            ->limit(15)
            ->find();
    }

    /**
     * Cari satu produk tepat berdasarkan barcode
     */
    public function findByBarcode(string $barcode)
    {
        return $this->where('barcode', $barcode)->first();
    }

    /**
     * Ambil daftar produk yang stoknya menipis (stok <= min_stock)
     */
    public function getLowStockProducts()
    {
        return $this->select('produk.*, kategori.nama_kategori')
            ->join('kategori', 'kategori.id = produk.kategori_id', 'left')
            ->where('produk.stok <= produk.min_stock')
            ->orderBy('produk.stok', 'ASC')
            ->findAll();
    }

    /**
     * Hitung total produk berstok kritis
     */
    public function countLowStock(): int
    {
        return $this->where('stok <= min_stock')->countAllResults();
    }
}

