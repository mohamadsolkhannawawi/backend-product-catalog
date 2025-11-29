<?php
/**
 * DomPDF Header & Footer Template for Catalozy
 * 
 * Usage in Laravel DomPDF:
 * $pdf = PDF::loadView('pdf.header-footer', [
 *     'reportTitle' => 'Laporan Produk',
 *     'reportDate' => now()->format('d M Y'),
 * ]);
 * 
 * For header/footer on every page, wrap content in HTML/CSS with fixed positioning
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #2D2F31;
            font-size: 12px;
            line-height: 1.6;
        }

        @page {
            size: A4;
            margin: 80px 25px 60px 25px;
        }

        /* ==================== HEADER ==================== */
        
        header {
            position: fixed;
            top: -60px;
            left: 0;
            right: 0;
            height: 60px;
            width: 100%;
            padding: 15px 25px;
            border-bottom: 3px solid #A435F0;
            background-color: #FFFFFF;
            display: flex;
            align-items: center;
        }

        .header-content {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-left {
            flex: 1;
        }

        .header-right {
            flex: 1;
            text-align: right;
        }

        .logo {
            font-size: 26px;
            font-weight: 700;
            color: #A435F0;
            letter-spacing: -0.5px;
            line-height: 1;
            margin-bottom: 2px;
        }

        .tagline {
            font-size: 10px;
            font-style: italic;
            color: #7A7C80;
        }

        .company-name {
            font-size: 12px;
            font-weight: 700;
            color: #2D2F31;
            margin-bottom: 2px;
        }

        .company-info {
            font-size: 10px;
            color: #5A5D62;
            line-height: 1.4;
        }

        .company-contact {
            font-size: 10px;
            color: #5A5D62;
            margin-top: 2px;
        }

        /* ==================== FOOTER ==================== */

        footer {
            position: fixed;
            bottom: -50px;
            left: 0;
            right: 0;
            height: 50px;
            width: 100%;
            padding: 10px 25px;
            border-top: 1px solid #D1D7DC;
            background-color: #FFFFFF;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-left {
            flex: 1;
        }

        .footer-right {
            flex: 1;
            text-align: right;
        }

        .system-info {
            font-size: 9px;
            color: #7A7C80;
            line-height: 1.4;
        }

        .copyright {
            font-size: 8px;
            color: #A0A5AA;
            margin-top: 3px;
        }

        .pagination {
            font-size: 11px;
            color: #2D2F31;
            font-weight: 600;
        }

        /* ==================== MAIN CONTENT ==================== */

        main {
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .report-title {
            font-size: 16px;
            font-weight: 700;
            color: #2D2F31;
            margin-bottom: 4px;
            padding-bottom: 8px;
            border-bottom: 2px solid #A435F0;
        }

        .report-date {
            font-size: 11px;
            color: #7A7C80;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #2D2F31;
            margin-top: 15px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #E5E7EB;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #A435F0;
            color: #FFFFFF;
            padding: 8px;
            text-align: left;
            font-weight: 700;
            font-size: 10px;
            border: 1px solid #A435F0;
        }

        td {
            padding: 8px;
            border: 1px solid #E5E7EB;
            font-size: 10px;
            color: #2D2F31;
        }

        tbody tr:nth-child(odd) {
            background-color: #F8F9FA;
        }

        tbody tr:nth-child(even) {
            background-color: #FFFFFF;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Page Break */
        .page-break {
            page-break-after: always;
        }

        /* Print Styles */
        @media print {
            body, html {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header>
        <div class="header-content">
            <div class="header-left">
                <div class="logo">Catalozy</div>
                <div class="tagline">Catalog Produk Andalan GenZ</div>
            </div>
            <div class="header-right">
                <div class="company-name">PT Catalozy Digital Indonesia</div>
                <div class="company-info">
                    Jl. Teknologi No. 12, Tembalang<br>
                    Kota Semarang, Jawa Tengah 50275
                </div>
                <div class="company-contact">
                    support@catalozy.id | www.catalozy.id
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main>
        <div class="report-title">{{ $reportTitle ?? 'Laporan Catalozy' }}</div>
        <div class="report-date">Periode: {{ $reportDate ?? now()->format('d M Y') }}</div>

        {{ $slot ?? '' }}
    </main>

    <!-- FOOTER -->
    <footer>
        <div class="footer-left">
            <div class="system-info">
                <div>Dicetak otomatis oleh Sistem Catalozy</div>
                <div>{{ now()->format('d M Y H:i') }}</div>
            </div>
            <div class="copyright">© 2025 Catalozy Digital Indonesia. Dilindungi Undang-Undang.</div>
        </div>
        <div class="footer-right">
            <div class="pagination">
                Halaman <span class="page-number"></span>
            </div>
        </div>
    </footer>
</body>
</html>
