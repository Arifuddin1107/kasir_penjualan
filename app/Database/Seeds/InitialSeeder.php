<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // 1. Users Default
        $users = [
            [
                'username'              => 'admin',
                'nama_lengkap'          => 'Administrator Owner',
                'password'              => password_hash('admin123', PASSWORD_BCRYPT),
                'role'                  => 'admin',
                'is_active'             => 1,
                'failed_login_attempts' => 0,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
            [
                'username'              => 'kasir',
                'nama_lengkap'          => 'Kasir Toko',
                'password'              => password_hash('kasir123', PASSWORD_BCRYPT),
                'role'                  => 'kasir',
                'is_active'             => 1,
                'failed_login_attempts' => 0,
                'created_at'            => $now,
                'updated_at'            => $now,
            ],
        ];

        $this->db->table('users')->insertBatch($users);
        $adminId = $this->db->insertID();

        // 2. Kategori Default
        $kategori = [
            ['nama_kategori' => 'Minuman', 'created_at' => $now, 'updated_at' => $now],
            ['nama_kategori' => 'Makanan Ringan', 'created_at' => $now, 'updated_at' => $now],
            ['nama_kategori' => 'Kebutuhan Dapur', 'created_at' => $now, 'updated_at' => $now],
            ['nama_kategori' => 'Personal Care', 'created_at' => $now, 'updated_at' => $now],
            ['nama_kategori' => 'ATK & Kantor', 'created_at' => $now, 'updated_at' => $now],
        ];
        $this->db->table('kategori')->insertBatch($kategori);

        // 3. Produk Default (beragam: stok cukup, dan satu dua dengan stok menipis <= min_stock untuk menguji indikator visual)
        $produkList = [
            [
                'kategori_id' => 1,
                'barcode'     => '8992753101111',
                'nama_produk' => 'Le Minerale 600ml',
                'harga_beli'  => 2800.00,
                'harga_jual'  => 3500.00,
                'stok'        => 48,
                'min_stock'   => 10,
                'satuan'      => 'btl',
                'deskripsi'   => 'Air mineral kemasan 600ml',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'kategori_id' => 1,
                'barcode'     => '8993175538112',
                'nama_produk' => 'Teh Botol Sosro 450ml',
                'harga_beli'  => 4500.00,
                'harga_jual'  => 6000.00,
                'stok'        => 24,
                'min_stock'   => 8,
                'satuan'      => 'btl',
                'deskripsi'   => 'Minuman teh melati dalam kemasan botol',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'kategori_id' => 2,
                'barcode'     => '8996001304123',
                'nama_produk' => 'Chitato Sapi Panggang 68g',
                'harga_beli'  => 8500.00,
                'harga_jual'  => 11000.00,
                'stok'        => 15,
                'min_stock'   => 5,
                'satuan'      => 'pcs',
                'deskripsi'   => 'Keripik kentang rasa sapi panggang',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'kategori_id' => 2,
                'barcode'     => '8992775211029',
                'nama_produk' => 'Oreo Vanilla 119.6g',
                'harga_beli'  => 7800.00,
                'harga_jual'  => 9500.00,
                'stok'        => 4, // Menipis: di bawah min_stock 5
                'min_stock'   => 5,
                'satuan'      => 'pcs',
                'deskripsi'   => 'Biskuit sandwich krim rasa vanilla',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'kategori_id' => 3,
                'barcode'     => '8999999001234',
                'nama_produk' => 'Minyak Goreng Bimoli 1L',
                'harga_beli'  => 17000.00,
                'harga_jual'  => 20000.00,
                'stok'        => 3, // Menipis: di bawah min_stock 6
                'min_stock'   => 6,
                'satuan'      => 'pch',
                'deskripsi'   => 'Minyak goreng kelapa sawit pouch 1 liter',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'kategori_id' => 3,
                'barcode'     => '8998866102315',
                'nama_produk' => 'Indomie Goreng Original 85g',
                'harga_beli'  => 2800.00,
                'harga_jual'  => 3500.00,
                'stok'        => 100,
                'min_stock'   => 20,
                'satuan'      => 'bks',
                'deskripsi'   => 'Mi instan goreng rasa original',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'kategori_id' => 4,
                'barcode'     => '8999999123456',
                'nama_produk' => 'Lifebuoy Sabun Cair Total 10 450ml',
                'harga_beli'  => 19500.00,
                'harga_jual'  => 24000.00,
                'stok'        => 18,
                'min_stock'   => 5,
                'satuan'      => 'pch',
                'deskripsi'   => 'Sabun cair antibakteri refill 450ml',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];

        foreach ($produkList as $p) {
            $this->db->table('produk')->insert($p);
            $pId = $this->db->insertID();

            // Catat mutasi awal 'in'
            $this->db->table('mutasi_stok')->insert([
                'produk_id'    => $pId,
                'user_id'      => 1, // Admin
                'tipe'         => 'in',
                'kuantitas'    => $p['stok'],
                'stok_sebelum' => 0,
                'stok_sesudah' => $p['stok'],
                'nomor_faktur' => 'INIT-STOCK',
                'keterangan'   => 'Stok awal sistem',
                'created_at'   => $now,
            ]);
        }

        // 4. Pengaturan Default
        $this->db->table('pengaturan')->insert([
            'nama_toko'    => 'Antigravity Retail Mart',
            'alamat'       => 'Jl. Emerald Avenue No. 108, Kota Digital',
            'no_telp'      => '0812-3456-7890',
            'footer_struk' => 'Terima kasih telah berbelanja di Antigravity Mart!',
            'paper_size'   => '58mm',
            'updated_at'   => $now,
        ]);
    }
}

