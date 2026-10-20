<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanModel extends Model
{
    protected $table            = 'penjualan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_faktur',
        'user_id',
        'tanggal',
        'total_item',
        'total_omzet',
        'total_hpp',
        'laba_kotor',
        'bayar',
        'kembalian',
        'metode_pembayaran',
        'catatan',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate Nomor Faktur unik format: INV-YYYYMMDD-XXXX
     */
    public function generateNomorFaktur(): string
    {
        $prefix = 'INV-' . date('Ymd') . '-';
        $last = $this->like('nomor_faktur', $prefix, 'after')
            ->orderBy('id', 'DESC')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last['nomor_faktur'], -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }

    /**
     * Ambil data penjualan dengan info kasir
     */
    public function getWithKasir(?int $id = null, ?string $startDate = null, ?string $endDate = null)
    {
        $builder = $this->select('penjualan.*, users.nama_lengkap as nama_kasir, users.username as username_kasir')
            ->join('users', 'users.id = penjualan.user_id', 'left');

        if ($id !== null) {
            return $builder->where('penjualan.id', $id)->first();
        }

        if ($startDate) {
            $builder->where('penjualan.tanggal >=', $startDate . ' 00:00:00');
        }

        if ($endDate) {
            $builder->where('penjualan.tanggal <=', $endDate . ' 23:59:59');
        }

        return $builder->orderBy('penjualan.tanggal', 'DESC')->findAll();
    }

    /**
     * Ambil statistik finansial untuk dashboard (omzet, HPP, laba kotor)
     */
    public function getFinancialSummary(?string $startDate = null, ?string $endDate = null)
    {
        $builder = $this->selectSum('total_omzet', 'total_omzet')
            ->selectSum('total_hpp', 'total_hpp')
            ->selectSum('laba_kotor', 'laba_kotor')
            ->selectCount('id', 'total_transaksi');

        if ($startDate) {
            $builder->where('tanggal >=', $startDate . ' 00:00:00');
        }

        if ($endDate) {
            $builder->where('tanggal <=', $endDate . ' 23:59:59');
        }

        $res = $builder->first();

        return [
            'total_transaksi' => (int) ($res['total_transaksi'] ?? 0),
            'total_omzet'     => (float) ($res['total_omzet'] ?? 0),
            'total_hpp'       => (float) ($res['total_hpp'] ?? 0),
            'laba_kotor'      => (float) ($res['laba_kotor'] ?? 0),
        ];
    }

    /**
     * Ambil data grafik penjualan 7 hari terakhir
     */
    public function getSalesChartData(int $days = 7)
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $summary = $this->getFinancialSummary($date, $date);
            $data[] = [
                'tanggal'    => date('d/m', strtotime($date)),
                'omzet'      => $summary['total_omzet'],
                'laba_kotor' => $summary['laba_kotor'],
            ];
        }
        return $data;
    }
}

