@extends('pdf.layouts.formal-report', [
    'reportTitle' => 'LAPORAN DAFTAR TOKO BERDASARKAN LOKASI PROVINSI',
    'reportDate' => now()->format('d-m-Y'),
])

@section('content')
<table>
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 35%;">Nama Toko</th>
            <th style="width: 30%;">Nama PIC</th>
            <th style="width: 30%;">Provinsi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data ?? [] as $index => $seller)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ $seller->store_name ?? '-' }}</td>
            <td>{{ $seller->pic_name ?? '-' }}</td>
            <td>{{ $seller->province->name ?? 'N/A' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center">Tidak ada data</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
