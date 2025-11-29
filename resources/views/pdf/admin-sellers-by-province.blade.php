@extends('pdf.layouts.report', [
    'reportTitle' => 'Laporan Penjual Berdasarkan Provinsi',
    'reportDate' => now()->format('d M Y'),
])

@section('content')
    <div class="section-title">Ringkasan Data</div>
    <table>
        <thead>
            <tr>
                <th>Metrik</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Penjual</td>
                <td class="text-right">{{ $sellersCount ?? 0 }}</td>
            </tr>
            <tr>
                <td>Total Provinsi</td>
                <td class="text-right">{{ $provincesCount ?? 0 }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Detail Penjual Berdasarkan Provinsi</div>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Provinsi</th>
                <th>Jumlah Penjual</th>
                <th class="text-right">Persentase</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sellers ?? [] as $index => $seller)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $seller->province_name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $seller->seller_count ?? 0 }}</td>
                    <td class="text-right">{{ number_format(($seller->seller_count / ($sellersCount ?: 1)) * 100, 2) }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data tersedia</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(isset($sellerDetails) && count($sellerDetails) > 0)
        <div class="section-title" style="margin-top: 25px;">Detail Penjual Terdaftar</div>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Toko</th>
                    <th>Pemilik</th>
                    <th>Provinsi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sellerDetails as $index => $detail)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $detail->store_name ?? 'N/A' }}</td>
                        <td>{{ $detail->owner_name ?? 'N/A' }}</td>
                        <td>{{ $detail->province_name ?? 'N/A' }}</td>
                        <td>
                            @if($detail->status === 'approved')
                                Disetujui
                            @elseif($detail->status === 'rejected')
                                Ditolak
                            @else
                                Menunggu
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div style="margin-top: 30px; border-top: 1px solid #E5E7EB; padding-top: 15px;">
        <p style="font-size: 10px; color: #7A7C80; margin-bottom: 5px;">
            <strong>Catatan:</strong> Laporan ini dihasilkan secara otomatis oleh sistem Catalozy dan merupakan data resmi perusahaan.
        </p>
        <p style="font-size: 9px; color: #A0A5AA;">
            Untuk informasi lebih lanjut, hubungi tim support@catalozy.id
        </p>
    </div>
@endsection
        }
        .note {
            font-size: 10px;
            color: #666;
            font-style: italic;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">SRS-MartPlace-10</div>
        <div class="title">Laporan Daftar Toko Berdasarkan Lokasi Propinsi</div>
        <div class="info">
            Tanggal dibuat: {{ \Carbon\Carbon::now()->format('d-m-Y') }} oleh {{ auth()->user()->name ?? 'System' }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 35%;">Nama Toko</th>
                <th style="width: 35%;">Nama PIC</th>
                <th style="width: 25%;">Propinsi</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Group by province and sort
                $tablePrefix = config('laravolt.indonesia.table_prefix', 'indonesia_');
                $provincesTable = $tablePrefix . 'provinces';
                $groupedSellers = collect($data)
                    ->groupBy(function ($seller) use ($provincesTable) {
                        return \DB::table($provincesTable)
                            ->where('code', $seller->province_id)
                            ->value('name') ?? $seller->province_id;
                    })
                    ->sortKeys();
                $counter = 0;
            @endphp
            @forelse($groupedSellers as $province => $sellers)
                @foreach($sellers as $seller)
                    @php $counter++ @endphp
                    <tr>
                        <td>{{ $counter }}</td>
                        <td>{{ $seller->store_name }}</td>
                        <td>{{ $seller->user->name ?? 'N/A' }}</td>
                        <td>{{ $province }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="note">
        ***) Data diurutkan berdasarkan propinsi
    </div>

    <div class="footer">
        Generated by SRS-MartPlace System
    </div>
</body>
</html>
