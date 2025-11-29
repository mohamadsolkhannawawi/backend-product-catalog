@extends('pdf.layouts.report', [
    'reportTitle' => $reportTitle ?? 'Laporan Stok Produk',
    'reportDate' => $reportDate ?? now()->format('d M Y'),
])

@section('content')
<div class="seller-stock-report">
    <!-- Store Info -->
    <div style="margin-bottom: 20px; padding: 12px; background-color: #F9F9F9; border-radius: 4px;">
        <div style="font-weight: 600; color: #2D2F31;">Toko: {{ $seller->store_name ?? 'N/A' }}</div>
        <div style="font-size: 12px; color: #666;">Pemilik: {{ $seller->user->name ?? 'N/A' }}</div>
    </div>

    <!-- Stock Table -->
    <div class="section-title">Daftar Stok Produk</div>
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
                    {{ number_format($product->avg_rating, 1) }} ★
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding: 15px; text-align: center; color: #666;">Belum ada produk</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Stock Summary -->
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-top: 20px;">
        <div style="padding: 15px; background-color: #D4EDDA; border-left: 4px solid #28A745; border-radius: 4px;">
            <div style="font-size: 12px; color: #155724; margin-bottom: 5px;">Stok Aman (> 5)</div>
            <div style="font-size: 20px; font-weight: bold; color: #155724;">
                {{ count(array_filter($data?->toArray() ?? [], fn($p) => $p['stock'] > 5)) }}
            </div>
        </div>
        <div style="padding: 15px; background-color: #FFF3CD; border-left: 4px solid #FFC107; border-radius: 4px;">
            <div style="font-size: 12px; color: #856404; margin-bottom: 5px;">Stok Terbatas (1-5)</div>
            <div style="font-size: 20px; font-weight: bold; color: #856404;">
                {{ count(array_filter($data?->toArray() ?? [], fn($p) => $p['stock'] > 0 && $p['stock'] <= 5)) }}
            </div>
        </div>
        <div style="padding: 15px; background-color: #F8D7DA; border-left: 4px solid #DC3545; border-radius: 4px;">
            <div style="font-size: 12px; color: #721C24; margin-bottom: 5px;">Stok Habis (0)</div>
            <div style="font-size: 20px; font-weight: bold; color: #721C24;">
                {{ count(array_filter($data?->toArray() ?? [], fn($p) => $p['stock'] == 0)) }}
            </div>
        </div>
    </div>

    <!-- Recommendations -->
    <div style="margin-top: 30px; padding: 15px; background-color: #E7F3FF; border: 1px solid #B3D9FF; border-radius: 4px;">
        <div style="font-weight: 600; color: #004085; margin-bottom: 8px;">💡 Rekomendasi</div>
        <ul style="margin: 0; padding-left: 20px; color: #004085; font-size: 12px;">
            <li>Segera restock produk dengan stok terbatas (1-5 unit)</li>
            <li>Produk dengan 0 stok harus diupdate atau diaktifkan kembali</li>
            <li>Pertahankan minimal 10 unit untuk produk best-seller</li>
            <li>Monitor pergerakan stok secara mingguan</li>
        </ul>
    </div>
</div>
@endsection
