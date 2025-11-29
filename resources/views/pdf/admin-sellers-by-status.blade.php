@extends('pdf.layouts.formal-report', [
    'reportTitle' => 'LAPORAN DAFTAR AKUN PENJUAL BERDASARKAN STATUS',
    'reportDate' => now()->format('d-m-Y'),
])

@section('content')
<table>
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 20%;">Nama User</th>
            <th style="width: 25%;">Nama PIC</th>
            <th style="width: 25%;">Nama Toko</th>
            <th style="width: 25%;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data ?? [] as $index => $seller)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ $seller->user->name ?? '-' }}</td>
            <td>{{ $seller->pic_name ?? '-' }}</td>
            <td>{{ $seller->store_name ?? '-' }}</td>
            <td>{{ ucfirst($seller->status ?? 'unknown') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">Tidak ada data</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
