<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk_<?= esc($penjualan['nomor_faktur']) ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace, sans-serif;
            font-size: 12px;
            color: #000000;
            background-color: #F3F4F6;
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        .receipt-card {
            background-color: #FFFFFF;
            width: <?= ($paperSize === '80mm') ? '78mm' : '56mm' ?>;
            padding: 10px 8px;
            border: 1px solid #D1D5DB;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }

        .store-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .store-sub {
            font-size: 10px;
            line-height: 1.3;
        }

        .divider {
            border-top: 1px dashed #000000;
            margin: 8px 0;
        }

        .meta-table, .item-table, .total-table {
            width: 100%;
            font-size: 11px;
            border-collapse: collapse;
        }

        .meta-table td, .total-table td {
            padding: 1px 0;
        }

        .item-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .footer-note {
            font-size: 10px;
            text-align: center;
            margin-top: 10px;
            line-height: 1.4;
        }

        .no-print-bar {
            position: fixed;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 8px;
            z-index: 100;
        }

        .btn-print {
            background-color: #059669;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            font-family: sans-serif;
            font-weight: bold;
        }

        .btn-close {
            background-color: #4B5563;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            font-family: sans-serif;
        }

        /* Thermal Print Styles (FR-4.1) */
        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .receipt-card {
                border: none;
                box-shadow: none;
                width: 100%;
                padding: 0;
            }
            @page {
                size: <?= ($paperSize === '80mm') ? '80mm' : '58mm' ?> auto;
                margin: 2mm;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak</button>
        <button class="btn-close" onclick="window.close()">✕ Tutup</button>
    </div>

    <div class="receipt-card">
        <!-- Store Header -->
        <div class="text-center">
            <div class="store-name"><?= esc($settings['nama_toko'] ?? 'ANTIGRAVITY RETAIL MART') ?></div>
            <?php if (!empty($settings['alamat'])): ?>
                <div class="store-sub"><?= esc($settings['alamat']) ?></div>
            <?php endif; ?>
            <?php if (!empty($settings['no_telp'])): ?>
                <div class="store-sub">Telp: <?= esc($settings['no_telp']) ?></div>
            <?php endif; ?>
        </div>

        <div class="divider"></div>

        <!-- Meta Transaksi -->
        <table class="meta-table">
            <tr>
                <td>Faktur</td>
                <td>: <?= esc($penjualan['nomor_faktur']) ?></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: <?= date('d/m/Y H:i', strtotime($penjualan['tanggal'])) ?></td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>: <?= esc($penjualan['nama_kasir'] ?? 'Kasir') ?></td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- Daftar Belanja -->
        <table class="item-table">
            <?php foreach ($items as $it): ?>
                <tr>
                    <td colspan="2"><b><?= esc($it['nama_produk']) ?></b></td>
                </tr>
                <tr>
                    <td><?= $it['qty'] ?> x <?= number_format($it['harga_jual'], 0, ',', '.') ?></td>
                    <td class="text-right"><?= number_format($it['subtotal_omzet'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div class="divider"></div>

        <!-- Total & Pembayaran -->
        <table class="total-table">
            <tr>
                <td>Total Item</td>
                <td class="text-right"><?= $penjualan['total_item'] ?></td>
            </tr>
            <tr style="font-size: 13px; font-weight: bold;">
                <td>Grand Total</td>
                <td class="text-right">Rp <?= number_format($penjualan['total_omzet'], 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>Metode</td>
                <td class="text-right"><?= strtoupper(esc($penjualan['metode_pembayaran'])) ?></td>
            </tr>
            <tr>
                <td>Dibayar</td>
                <td class="text-right">Rp <?= number_format($penjualan['bayar'], 0, ',', '.') ?></td>
            </tr>
            <tr style="font-weight: bold;">
                <td>Kembalian</td>
                <td class="text-right">Rp <?= number_format($penjualan['kembalian'], 0, ',', '.') ?></td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- Footer -->
        <div class="footer-note">
            <?= nl2br(esc($settings['footer_struk'] ?? 'Terima kasih atas kunjungan Anda!')) ?><br>
            <i>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</i>
        </div>
    </div>

    <script>
        // Otomatis munculkan print dialog saat struk dibuka (FR-4.1)
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        };
    </script>
</body>
</html>

