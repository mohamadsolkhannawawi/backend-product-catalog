@extends('pdf.layouts.formal-report', [
    'reportTitle' => 'LAPORAN DAFTAR PRODUK BERDASARKAN RATING',
    'reportDate' => now()->format('d-m-Y'),
])

@section('content')
<table>
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 18%;">Produk</th>
            <th style="width: 12%;">Kategori</th>
            <th style="width: 12%;">Harga</th>
            <th style="width: 8%;">Rating</th>
            <th style="width: 20%;">Nama Toko</th>
            <th style="width: 25%;">Provinsi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data ?? [] as $index => $product)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ $product->name ?? '-' }}</td>
            <td>{{ $product->category->name ?? '-' }}</td>
            <td class="text-right">Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}</td>
            <td class="text-center">{{ number_format($product->avg_rating ?? 0, 1) }}</td>
            <td>{{ $product->seller->store_name ?? '-' }}</td>
            <td>{{ $product->reviewer_province ?? 'N/A' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center">Tidak ada data</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
