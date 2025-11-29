@extends('pdf.layouts.formal-report', [
    'reportTitle' => 'LAPORAN DAFTAR PRODUK SEGERA DIPESAN',
    'reportDate' => now()->format('d-m-Y'),
])

@section('content')
<table>
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 28%;">Produk</th>
            <th style="width: 20%;">Kategori</th>
            <th style="width: 22%;">Harga</th>
            <th style="width: 25%;">Stok</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data ?? [] as $index => $product)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ $product->name ?? '-' }}</td>
            <td>{{ $product->category->name ?? '-' }}</td>
            <td class="text-right">Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}</td>
            <td class="text-center">{{ $product->stock ?? 0 }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">Tidak ada data</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
