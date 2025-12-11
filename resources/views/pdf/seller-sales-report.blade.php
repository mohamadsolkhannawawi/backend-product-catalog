@extends('pdf.layouts.formal-report', [
    'reportTitle' => $reportTitle ?? 'LAPORAN DAFTAR PRODUK SEGERA DIPESAN',
    'reportDate' => $reportDate ?? now()->format('d-m-Y'),
])

@section('content')
<table>
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 35%;">Produk</th>
            <th style="width: 25%;">Kategori</th>
            <th style="width: 20%;">Harga</th>
            <th style="width: 15%;">Stok</th>
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
            <td colspan="5" class="text-center">Tidak ada produk yang perlu dipesan</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top: 20pt; font-size: 9pt; color: #666;">
    <p>***) Daftar produk dengan stok kurang dari 2 unit, diurutkan berdasarkan kategori dan nama produk</p>
</div>
@endsection
