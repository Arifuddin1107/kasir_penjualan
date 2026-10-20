<?php

namespace App\Controllers;

use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Laporan extends BaseController
{
    protected $penjualanModel;
    protected $detailModel;

    public function __construct()
    {
        $this->penjualanModel = new PenjualanModel();
        $this->detailModel    = new PenjualanDetailModel();
    }

    public function index()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-d');

        $transaksi = $this->penjualanModel->getWithKasir(null, $startDate, $endDate);
        $summary   = $this->penjualanModel->getFinancialSummary($startDate, $endDate);

        $marginPersen = ($summary['total_omzet'] > 0) 
            ? round(($summary['laba_kotor'] / $summary['total_omzet']) * 100, 2) 
            : 0;

        $data = [
            'title'        => 'Laporan Transaksi & Analitik Finansial — Antigravity Retail',
            'transaksi'    => $transaksi,
            'summary'      => $summary,
            'marginPersen' => $marginPersen,
            'startDate'    => $startDate,
            'endDate'      => $endDate,
        ];

        return view('admin/laporan/index', $data);
    }

    /**
     * Ekspor Laporan ke Berkas Excel (.xlsx) dengan PhpSpreadsheet (FR-5.2)
     */
    public function exportExcel()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-d');

        $transaksi = $this->penjualanModel->getWithKasir(null, $startDate, $endDate);
        $summary   = $this->penjualanModel->getFinancialSummary($startDate, $endDate);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Penjualan');

        // Header Judul
        $sheet->setCellValue('A1', 'LAPORAN TRANSAKSI PENJUALAN & LABA KOTOR');
        $sheet->setCellValue('A2', "Periode: {$startDate} s/d {$endDate}");
        $sheet->setCellValue('A3', 'Diekspor pada: ' . date('d/m/Y H:i:s'));

        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('064E3B');
        $sheet->getStyle('A2:A3')->getFont()->setSize(10)->getColor()->setRGB('4B5563');

        // Header Tabel Kolom
        $headers = [
            'A5' => 'No',
            'B5' => 'No Faktur',
            'C5' => 'Tanggal & Waktu',
            'D5' => 'Kasir',
            'E5' => 'Total Item',
            'F5' => 'Total Omzet (Rp)',
            'G5' => 'Total HPP (Rp)',
            'H5' => 'Laba Kotor (Rp)',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // Style Header Tabel (Tema Emerald Minimalist: Hijau #059669 dengan teks putih)
        $sheet->getStyle('A5:H5')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(26);

        // Isi Data Transaksi
        $row = 6;
        $no = 1;
        foreach ($transaksi as $t) {
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $t['nomor_faktur']);
            $sheet->setCellValue("C{$row}", date('d/m/Y H:i', strtotime($t['tanggal'])));
            $sheet->setCellValue("D{$row}", $t['nama_kasir'] ?? 'System');
            $sheet->setCellValue("E{$row}", (int) $t['total_item']);
            $sheet->setCellValue("F{$row}", (float) $t['total_omzet']);
            $sheet->setCellValue("G{$row}", (float) $t['total_hpp']);
            $sheet->setCellValue("H{$row}", (float) $t['laba_kotor']);

            // Formatting
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("F{$row}:H{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("F{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $row++;
        }

        // Baris Total
        $totalRow = $row;
        $sheet->setCellValue("A{$totalRow}", 'TOTAL AKUMULASI');
        $sheet->mergeCells("A{$totalRow}:D{$totalRow}");

        $sheet->setCellValue("E{$totalRow}", "=SUM(E6:E" . ($totalRow - 1) . ")");
        $sheet->setCellValue("F{$totalRow}", "=SUM(F6:F" . ($totalRow - 1) . ")");
        $sheet->setCellValue("G{$totalRow}", "=SUM(G6:G" . ($totalRow - 1) . ")");
        $sheet->setCellValue("H{$totalRow}", "=SUM(H6:H" . ($totalRow - 1) . ")");

        $sheet->getStyle("A{$totalRow}:H{$totalRow}")->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => '064E3B'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'ECFDF5'],
            ],
        ]);
        $sheet->getStyle("E{$totalRow}:H{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');

        // Border Tabel
        $sheet->getStyle("A5:H{$totalRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'D1FAE5'],
                ],
            ],
        ]);

        // Auto-size kolom
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Tulis file ke output stream browser
        $filename = "Laporan_Penjualan_{$startDate}_{$endDate}.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}

