<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $reportTitle ?? 'Laporan' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            background: white;
        }

        @page {
            margin: 20mm 15mm 25mm 15mm;
            size: A4;
        }

        .page-footer {
            position: fixed;
            bottom: 10mm;
            right: 15mm;
            width: auto;
            font-size: 9pt;
            color: #666;
            text-align: right;
        }

        .page-container {
            margin-left: 15pt;
            margin-right: 15pt;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 15pt;
            margin-left: 15pt;
            margin-right: 15pt;
            border-bottom: 2px solid #000;
            padding-bottom: 10pt;
        }

        .header-company {
            font-weight: bold;
            font-size: 12pt;
            margin-bottom: 3pt;
        }

        .header-address {
            font-size: 9pt;
            color: #333;
        }

        .header-contact {
            font-size: 9pt;
            color: #333;
        }

        /* Title */
        .report-title {
            text-align: center;
            font-weight: bold;
            font-size: 13pt;
            margin: 15pt 15pt 5pt 15pt;
        }

        /* Metadata */
        .report-meta {
            font-size: 10pt;
            margin-bottom: 15pt;
            margin-left: 15pt;
            margin-right: 15pt;
        }

        .report-meta p {
            margin: 3pt 0;
        }

        /* Table */
        table {
            width: calc(100% - 30pt);
            border-collapse: collapse;
            margin-top: 10pt;
            margin-bottom: 15pt;
            margin-left: 15pt;
            margin-right: 15pt;
        }

        table thead {
            background-color: #5a5a5a;
            color: white;
        }

        table th {
            border: 1px solid #5a5a5a;
            padding: 10pt 12pt;
            text-align: left;
            font-weight: bold;
            font-size: 10pt;
        }

        table td {
            border: 1px solid #d0d0d0;
            padding: 8pt 12pt;
            font-size: 10pt;
        }

        table tbody tr:nth-child(odd) {
            background-color: #f5f5f5;
        }

        table tbody tr:nth-child(even) {
            background-color: #fff;
        }

        /* Footer */
        .report-footer {
            font-size: 9pt;
            margin-top: 20pt;
            margin-left: 15pt;
            margin-right: 15pt;
            padding-top: 10pt;
            border-top: 1px solid #ccc;
            text-align: right;
            color: #666;
        }

        .footer-note {
            font-size: 8pt;
            margin-top: 10pt;
            margin-left: 15pt;
            margin-right: 15pt;
            color: #999;
        }

        /* Utility classes */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .page-break {
            page-break-after: always;
        }

        .no-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-company">PT CATALOZY DIGITAL INDONESIA</div>
        <div class="header-address">Jl. Teknologi No. 12, Semarang, Jawa Tengah 50188</div>
        <div class="header-contact">Telp: (024) 1234-5678 | Email: support@catalozy.id | Web: www.catalozy.id</div>
    </div>

    <!-- Title -->
    <div class="report-title">{{ $reportTitle ?? 'LAPORAN' }}</div>

    <!-- Metadata -->
    <div class="report-meta">
        <p>Tanggal dibuat: <strong>{{ \Carbon\Carbon::now('Asia/Jakarta')->format('d-m-Y') }}</strong> oleh <strong>{{ auth()->user()->name ?? 'Admin' }}</strong></p>
    </div>

    <!-- Content (yield from child template) -->
    @yield('content')

    <!-- Footer -->
    <div class="report-footer">
        <p>Dicetak otomatis oleh Sistem Catalozy</p>
        <p>{{ \Carbon\Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s') }} WIB</p>
    </div>

    <div class="footer-note">
        <p>***) Dokumen ini adalah laporan resmi dari Sistem Catalozy dan bersifat rahasia</p>
    </div>
</body>
</html>
