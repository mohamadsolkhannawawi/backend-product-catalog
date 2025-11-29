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
                <td class="text-right">{{ $totalSellers ?? 0 }}</td>
            </tr>
            <tr>
                <td>Total Provinsi</td>
                <td class="text-right">{{ $totalProvinces ?? 0 }}</td>
            </tr>
            <tr>
                <td>Penjual Disetujui</td>
                <td class="text-right">{{ $approvedSellers ?? 0 }}</td>
            </tr>
            <tr>
                <td>Penjual Ditolak</td>
                <td class="text-right">{{ $rejectedSellers ?? 0 }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Detail Penjual Berdasarkan Provinsi</div>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Provinsi</th>
                <th>Nama Toko</th>
                <th>Nama Pemilik</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sellersByProvince ?? [] as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['province_name'] ?? 'N/A' }}</td>
                    <td>{{ $item['store_name'] ?? 'N/A' }}</td>
                    <td>{{ $item['owner_name'] ?? 'N/A' }}</td>
                    <td>{{ $item['status'] ?? 'Menunggu' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data tersedia</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; border-top: 1px solid #E5E7EB; padding-top: 15px;">
        <p style="font-size: 10px; color: #7A7C80; margin-bottom: 5px;">
            <strong>Catatan:</strong> Laporan ini dihasilkan secara otomatis oleh sistem Catalozy dan merupakan data resmi perusahaan.
        </p>
        <p style="font-size: 9px; color: #A0A5AA;">
            Untuk informasi lebih lanjut, hubungi tim support@catalozy.id
        </p>
    </div>
@endsection
