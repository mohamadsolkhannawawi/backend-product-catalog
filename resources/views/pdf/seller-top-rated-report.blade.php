@extends('pdf.layouts.report', [
    'reportTitle' => $reportTitle ?? 'Laporan Produk Terpilih',
    'reportDate' => $reportDate ?? now()->format('d M Y'),
])

@section('content')
<div class="seller-top-rated-report">
    <!-- Store Info -->
    <div style="margin-bottom: 20px; padding: 12px; background-color: #F9F9F9; border-radius: 4px;">
        <div style="font-weight: 600; color: #2D2F31;">Toko: {{ $seller->store_name ?? 'N/A' }}</div>
        <div style="font-size: 12px; color: #666;">Pemilik: {{ $seller->user->name ?? 'N/A' }}</div>
    </div>

    <!-- Top Rated Table -->
    <div class="section-title">Produk dengan Rating Terbaik</div>
    <table class="data-table" style="width: 100%; margin-bottom: 20px;">
        <thead>
            <tr style="background-color: #A435F0; color: white;">
                <th style="padding: 10px; text-align: left; border: 1px solid #A435F0;">No</th>
                <th style="padding: 10px; text-align: left; border: 1px solid #A435F0;">Nama Produk</th>
                <th style="padding: 10px; text-align: left; border: 1px solid #A435F0;">Kategori</th>
                <th style="padding: 10px; text-align: right; border: 1px solid #A435F0;">Harga</th>
                <th style="padding: 10px; text-align: center; border: 1px solid #A435F0;">Stok</th>
                <th style="padding: 10px; text-align: center; border: 1px solid #A435F0;">Rating</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data ?? [] as $index => $product)
            <tr style="background-color: {{ $index % 2 === 0 ? '#FFFFFF' : '#F9F9F9' }}; border-bottom: 1px solid #E5E7EB;">
                <td style="padding: 10px; border: 1px solid #E5E7EB; text-align: center;">{{ $index + 1 }}</td>
                <td style="padding: 10px; border: 1px solid #E5E7EB;">{{ $product->name }}</td>
                <td style="padding: 10px; border: 1px solid #E5E7EB;">{{ $product->category->name ?? 'N/A' }}</td>
                <td style="padding: 10px; border: 1px solid #E5E7EB; text-align: right;">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td style="padding: 10px; border: 1px solid #E5E7EB; text-align: center;">
                    <span style="padding: 4px 8px; background-color: {{ $product->stock > 5 ? '#D4EDDA' : ($product->stock > 0 ? '#FFF3CD' : '#F8D7DA') }}; color: {{ $product->stock > 5 ? '#155724' : ($product->stock > 0 ? '#856404' : '#721C24') }}; border-radius: 3px; font-weight: 600;">
                        {{ $product->stock }}
                    </span>
                </td>
                <td style="padding: 10px; border: 1px solid #E5E7EB; text-align: center;">
                    <span style="padding: 4px 8px; background-color: #FFF8DC; color: #FF9800; border-radius: 3px; font-weight: 600;">
                        {{ number_format($product->avg_rating, 1) }} ★
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding: 15px; text-align: center; color: #666;">Belum ada produk</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Rating Summary -->
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-top: 20px;">
        <div style="padding: 15px; background-color: #FFF8E1; border-left: 4px solid #FFC107; border-radius: 4px;">
            <div style="font-size: 12px; color: #856404; margin-bottom: 5px;">Produk Terjual</div>
            <div style="font-size: 20px; font-weight: bold; color: #FF9800;">
                {{ count(array_filter($data?->toArray() ?? [], fn($p) => $p['avg_rating'] > 0)) }}
            </div>
        </div>
        <div style="padding: 15px; background-color: #E8F5E9; border-left: 4px solid #4CAF50; border-radius: 4px;">
            <div style="font-size: 12px; color: #2E7D32; margin-bottom: 5px;">Rating Rata-rata Tertinggi</div>
            <div style="font-size: 20px; font-weight: bold; color: #4CAF50;">
                {{ number_format(max($data?->pluck('avg_rating')->toArray() ?? [0]), 1) }} ★
            </div>
        </div>
        <div style="padding: 15px; background-color: #F3E5F5; border-left: 4px solid #A435F0; border-radius: 4px;">
            <div style="font-size: 12px; color: #6A1B9A; margin-bottom: 5px;">Total Produk</div>
            <div style="font-size: 20px; font-weight: bold; color: #A435F0;">
                {{ count($data ?? []) }}
            </div>
        </div>
    </div>

    <!-- Tips -->
    <div style="margin-top: 30px; padding: 15px; background-color: #E0F2F1; border: 1px solid #80CBC4; border-radius: 4px;">
        <div style="font-weight: 600; color: #00695C; margin-bottom: 8px;">⭐ Pertahankan Kualitas</div>
        <ul style="margin: 0; padding-left: 20px; color: #00695C; font-size: 12px;">
            <li>Produk dengan rating tinggi adalah aset bisnis Anda</li>
            <li>Promosikan produk ini lebih aktif di toko Anda</li>
            <li>Jaga kualitas dan konsistensi layanan</li>
            <li>Respons review pembeli dengan profesional</li>
        </ul>
    </div>
</div>
@endsection
