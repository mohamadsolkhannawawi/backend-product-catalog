<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Catalozy</title>
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
            line-height: 1.4;
        }

        /* Page Setup for DomPDF */
        @page {
            size: A4;
            margin: 100px 25px 80px 25px;
            
            @top-left {
                content: "";
            }
            
            @bottom-right {
                content: "Halaman " counter(page) " dari " counter(pages);
            }
        }

        /* ==================== HEADER SECTION ==================== */
        
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            background-color: #FFFFFF;
            padding: 20px 25px;
            z-index: 100;
        }

        .header-container {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }

        .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
            padding-left: 20px;
        }

        /* Left Side - Branding */
        .logo {
            font-size: 32px;
            font-weight: 700;
            color: #A435F0;
            letter-spacing: -1px;
            margin-bottom: 4px;
        }

        .tagline {
            font-size: 11px;
            font-style: italic;
            color: #7A7C80;
            font-weight: 400;
        }

        /* Right Side - Company Info */
        .company-name {
            font-size: 13px;
            font-weight: 700;
            color: #2D2F31;
            margin-bottom: 4px;
        }

        .company-address {
            font-size: 11px;
            color: #5A5D62;
            line-height: 1.5;
            margin-bottom: 4px;
        }

        .company-contact {
            font-size: 11px;
            color: #5A5D62;
        }

        /* Header Border */
        header::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 25px;
            right: 25px;
            height: 3px;
            background-color: #A435F0;
        }

        /* ==================== BODY CONTENT ==================== */

        main {
            margin-top: 40px;
            margin-bottom: 40px;
        }

        .content-placeholder {
            min-height: 400px;
            padding: 20px;
            border: 1px dashed #D1D7DC;
            background-color: #FAFBFC;
            text-align: center;
            color: #8A92A6;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ==================== TABLE STYLING (For Reports) ==================== */

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background-color: #A435F0;
            color: #FFFFFF;
            padding: 10px;
            text-align: left;
            font-weight: 700;
            font-size: 11px;
            border: 1px solid #A435F0;
        }

        td {
            padding: 10px;
            border: 1px solid #E5E7EB;
            font-size: 11px;
            color: #2D2F31;
        }

        tbody tr:nth-child(odd) {
            background-color: #F8F9FA;
        }

        tbody tr:nth-child(even) {
            background-color: #FFFFFF;
        }

        tbody tr:hover {
            background-color: #F0E6FF;
        }

        /* ==================== FOOTER SECTION ==================== */

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            background-color: #FFFFFF;
            padding: 15px 25px;
            border-top: 1px solid #D1D7DC;
            z-index: 100;
        }

        .footer-container {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        .footer-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: left;
        }

        .footer-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }

        /* Left Side - System Info */
        .system-info {
            font-size: 10px;
            color: #7A7C80;
            line-height: 1.6;
        }

        .system-info-line {
            margin-bottom: 2px;
        }

        .copyright {
            font-size: 9px;
            color: #A0A5AA;
            margin-top: 4px;
        }

        /* Right Side - Pagination */
        .pagination {
            font-size: 11px;
            color: #2D2F31;
            font-weight: 600;
        }

        .page-count {
            color: #7A7C80;
            font-weight: 400;
        }

        /* ==================== UTILITY CLASSES ==================== */

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #2D2F31;
            margin-bottom: 10px;
            margin-top: 20px;
            padding-bottom: 6px;
            border-bottom: 2px solid #A435F0;
        }

        /* Print Media Queries */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            header, footer {
                position: fixed;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header>
        <div class="header-container">
            <div class="header-left">
                <div class="logo">Catalozy</div>
                <div class="tagline">Catalog Produk Andalan GenZ</div>
            </div>
            <div class="header-right">
                <div class="company-name">PT Catalozy Digital Indonesia</div>
                <div class="company-address">
                    Tembalang, Kota Semarang<br>
                    Jawa Tengah 50275
                </div>
                <div class="company-contact">
                    support@catalozy.id | www.catalozy.id
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main>
        <!-- Example: Report Title -->
        <div class="mt-20">
            <h1 style="font-size: 18px; color: #2D2F31; margin-bottom: 5px;">Laporan Produk Catalozy</h1>
            <p style="font-size: 11px; color: #7A7C80;">Periode: [Tanggal Laporan]</p>
        </div>

        <!-- Example: Summary Section -->
        <div class="section-title">Ringkasan Laporan</div>
        <table>
            <tbody>
                <tr>
                    <td><strong>Total Produk</strong></td>
                    <td class="text-right">[Jumlah Produk]</td>
                </tr>
                <tr>
                    <td><strong>Total Penjual</strong></td>
                    <td class="text-right">[Jumlah Penjual]</td>
                </tr>
                <tr>
                    <td><strong>Total Review</strong></td>
                    <td class="text-right">[Jumlah Review]</td>
                </tr>
            </tbody>
        </table>

        <!-- Content Area Placeholder -->
        <div class="section-title">Data Laporan</div>
        <div class="content-placeholder">
            [Area Konten Laporan - Tabel atau Grafik akan ditampilkan di sini]
        </div>

        <!-- Example: Data Table -->
        <div class="section-title">Detail Data</div>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Penjual</th>
                    <th>Harga</th>
                    <th>Rating</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>[Nama Produk]</td>
                    <td>[Kategori]</td>
                    <td>[Nama Penjual]</td>
                    <td class="text-right">Rp [Harga]</td>
                    <td class="text-right">★★★★★</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>[Nama Produk]</td>
                    <td>[Kategori]</td>
                    <td>[Nama Penjual]</td>
                    <td class="text-right">Rp [Harga]</td>
                    <td class="text-right">★★★★☆</td>
                </tr>
            </tbody>
        </table>
    </main>

    <!-- FOOTER -->
    <footer>
        <div class="footer-container">
            <div class="footer-left">
                <div class="system-info">
                    <div class="system-info-line">Dicetak otomatis oleh Sistem Catalozy</div>
                    <div class="system-info-line">Tanggal: {{ date('d M Y H:i') }}</div>
                    <div class="copyright">© 2025 Catalozy Digital Indonesia. Dilindungi Undang-Undang.</div>
                </div>
            </div>
            <div class="footer-right">
                <div class="pagination">
                    Halaman <span style="font-weight: 700;">1</span> dari <span class="page-count">1</span>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
