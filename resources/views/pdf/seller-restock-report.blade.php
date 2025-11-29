@extends('pdf.layouts.report', [
    'reportTitle' => $reportTitle ?? 'Laporan Produk Perlu di-Restock',
    'reportDate' => $reportDate ?? now()->format('d M Y'),
])

@section('content')
<div class="seller-restock-report">
    <!-- Store Info -->
    <div style="margin-bottom: 20px; padding: 12px; background-color: #F9F9F9; border-radius: 4px;">
        <div style="font-weight: 600; color: #2D2F31;">Toko: {{ $seller->store_name ?? 'N/A' }}</div>
        <div style="font-size: 12px; color: #666;">Pemilik: {{ $seller->user->name ?? 'N/A' }}</div>
    </div>

    <!-- Warning Banner -->
    <div style="padding: 15px; background-color: #FEF2F2; border: 2px solid #DC3545; border-radius: 4px; margin-bottom: 20px;">
        <div style="font-weight: 700; color: #721C24; font-size: 14px;">⚠️ PERHATIAN: Produk dengan Stok < 2 Unit</div>
        <div style="font-size: 12px; color: #721C24; margin-top: 5px;">Produk-produk di bawah segera memerlukan restock untuk menghindari kehilangan penjualan.</div>
    </div>

    <!-- Restock Table -->
    <div class="section-title">Daftar Produk Perlu di-Restock</div>
    <table class="data-table" style="width: 100%; margin-bottom: 20px;">
        <thead>
            <tr style="background-color: #A435F0; color: white;">
                <th style="padding: 10px; text-align: left; border: 1px solid #A435F0;">No</th>
                <th style="padding: 10px; text-align: left; border: 1px solid #A435F0;">Nama Produk</th>
                <th style="padding: 10px; text-align: left; border: 1px solid #A435F0;">Kategori</th>
                <th style="padding: 10px; text-align: right; border: 1px solid #A435F0;">Harga</th>
                <th style="padding: 10px; text-align: center; border: 1px solid #A435F0; background-color: #DC3545;">Stok Saat Ini</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data ?? [] as $index => $product)
            <tr style="background-color: #FEF2F2; border-bottom: 1px solid #F5C6CB;">
                <td style="padding: 10px; border: 1px solid #F5C6CB; text-align: center; font-weight: 600;">{{ $index + 1 }}</td>
                <td style="padding: 10px; border: 1px solid #F5C6CB; font-weight: 500;">{{ $product->name }}</td>
                <td style="padding: 10px; border: 1px solid #F5C6CB;">{{ $product->category->name ?? 'N/A' }}</td>
                <td style="padding: 10px; border: 1px solid #F5C6CB; text-align: right;">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td style="padding: 10px; border: 1px solid #F5C6CB; text-align: center;">
                    <span style="padding: 6px 10px; background-color: #DC3545; color: white; border-radius: 3px; font-weight: 700; display: inline-block;">
                        {{ $product->stock }} Unit
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="padding: 15px; text-align: center; color: #28A745; font-weight: 600;">✓ Semua produk memiliki stok cukup</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Restock Summary -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 20px;">
        <div style="padding: 15px; background-color: #F8D7DA; border-left: 4px solid #DC3545; border-radius: 4px;">
            <div style="font-size: 12px; color: #721C24; margin-bottom: 5px;">Total Produk Perlu Restock</div>
            <div style="font-size: 24px; font-weight: bold; color: #721C24;">
                {{ count($data ?? []) }}
            </div>
        </div>
        <div style="padding: 15px; background-color: #FFF3CD; border-left: 4px solid #FFC107; border-radius: 4px;">
            <div style="font-size: 12px; color: #856404; margin-bottom: 5px;">Total Unit Kurang</div>
            <div style="font-size: 24px; font-weight: bold; color: #FF9800;">
                {{ $data?->sum('stock') ?? 0 }}
            </div>
        </div>
    </div>

    <!-- Action Items -->
    <div style="margin-top: 30px; padding: 15px; background-color: #E7F3FF; border: 1px solid #B3D9FF; border-radius: 4px;">
        <div style="font-weight: 600; color: #004085; margin-bottom: 8px;">📋 Tindakan Segera</div>
        <ol style="margin: 0; padding-left: 20px; color: #004085; font-size: 12px;">
            <li><strong>Hubungi supplier</strong> untuk melakukan pemesanan ulang</li>
            <li><strong>Prioritaskan restock</strong> untuk produk dengan penjualan tinggi</li>
            <li><strong>Update inventory</strong> di dashboard penjual setelah stock diterima</li>
            <li><strong>Monitor penjualan</strong> untuk menghindari kehabisan stok</li>
            <li><strong>Atur reminder</strong> stok minimum untuk produk-produk penting</li>
        </ol>
    </div>

    <!-- Notes -->
    <div style="margin-top: 20px; padding: 12px; background-color: #F0F0F0; border-radius: 4px; font-size: 11px; color: #666; border-left: 3px solid #666;">
        <strong>Catatan:</strong> Laporan ini menampilkan produk dengan stok kurang dari 2 unit. Setiap produk yang habis terjual akan berdampak pada tingkat penjualan dan kepuasan pelanggan.
    </div>
</div>
@endsection
