@extends('pdf.layouts.formal-report', [
    'reportTitle' => $reportTitle ?? 'LAPORAN DAFTAR PRODUK BERDASARKAN RATING',
    'reportDate' => $reportDate ?? now()->format('d-m-Y'),
])

@section('content')
<table>
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 28%;">Produk</th>
            <th style="width: 22%;">Kategori</th>
            <th style="width: 18%;">Harga</th>
            <th style="width: 12%;">Stok</th>
            <th style="width: 15%;">Rating</th>
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
            <td class="text-center"><strong>{{ number_format($product->avg_rating ?? 0, 1) }}/5</strong></td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">Tidak ada data produk</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top: 20pt; font-size: 9pt; color: #666;">
    <p>***) Daftar produk diurutkan berdasarkan rating tertinggi</p>
</div>
@endsection
