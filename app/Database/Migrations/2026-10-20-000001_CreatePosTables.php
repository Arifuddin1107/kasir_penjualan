<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePosTables extends Migration
{
    public function up()
    {
        // 1. Table Users
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'nama_lengkap' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'kasir'],
                'default'    => 'kasir',
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'failed_login_attempts' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 0,
            ],
            'locked_until' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users', true, ['ENGINE' => 'InnoDB']);

        // 2. Table Kategori
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('kategori', true, ['ENGINE' => 'InnoDB']);

        // 3. Table Produk
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kategori_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
            ],
            'barcode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'nama_produk' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'harga_beli' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'harga_jual' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'stok' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'min_stock' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 5,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'pcs',
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kategori_id', 'kategori', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('produk', true, ['ENGINE' => 'InnoDB']);

        // 4. Table Penjualan
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nomor_faktur' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'unique'     => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'tanggal' => [
                'type' => 'DATETIME',
            ],
            'total_item' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'total_omzet' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'total_hpp' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'laba_kotor' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'bayar' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'kembalian' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'metode_pembayaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'cash',
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('penjualan', true, ['ENGINE' => 'InnoDB']);

        // 5. Table Penjualan Detail
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'penjualan_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'produk_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'barcode' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'nama_produk' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'harga_beli' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'harga_jual' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'qty' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'subtotal_omzet' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'subtotal_hpp' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'subtotal_laba' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('penjualan_id', 'penjualan', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('produk_id', 'produk', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('penjualan_detail', true, ['ENGINE' => 'InnoDB']);

        // 6. Table Mutasi Stok
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'produk_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'tipe' => [
                'type'       => 'ENUM',
                'constraint' => ['in', 'out', 'sale', 'adjustment'],
            ],
            'kuantitas' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'stok_sebelum' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'stok_sesudah' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'nomor_faktur' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('produk_id', 'produk', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('mutasi_stok', true, ['ENGINE' => 'InnoDB']);

        // 7. Table Pengaturan
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_toko' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Retail Emerald POS',
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'no_telp' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'footer_struk' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'default'    => 'Terima kasih atas kunjungan Anda',
            ],
            'paper_size' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => '58mm',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('pengaturan', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('mutasi_stok', true);
        $this->forge->dropTable('penjualan_detail', true);
        $this->forge->dropTable('penjualan', true);
        $this->forge->dropTable('produk', true);
        $this->forge->dropTable('kategori', true);
        $this->forge->dropTable('pengaturan', true);
        $this->forge->dropTable('users', true);
    }
}

