@extends('pdf.layouts.report', [
    'reportTitle' => $reportTitle ?? 'Dashboard Penjual',
    'reportDate' => $reportDate ?? now()->format('d M Y'),
])

@section('content')
<div class="seller-dashboard-report">
    <!-- Store Information Section -->
    <div class="section-title">Informasi Toko</div>
    <table class="info-table" style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td style="width: 30%; font-weight: 500;">Nama Toko:</td>
            <td>{{ $seller->store_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="font-weight: 500;">Pemilik:</td>
            <td>{{ $seller->user->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="font-weight: 500;">Email:</td>
            <td>{{ $seller->user->email ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="font-weight: 500;">Status:</td>
            <td>
                <span style="padding: 3px 8px; background-color: {{ $seller->status === 'approved' ? '#D4EDDA' : '#FFF3CD' }}; color: {{ $seller->status === 'approved' ? '#155724' : '#856404' }}; border-radius: 3px;">
                    {{ ucfirst($seller->status ?? 'unknown') }}
                </span>
            </td>
        </tr>
        <tr>
            <td style="font-weight: 500;">Bergabung:</td>
            <td>{{ $seller->created_at?->format('d M Y') ?? 'N/A' }}</td>
        </tr>
    </table>

    <!-- Products Overview -->
    <div class="section-title" style="margin-top: 25px;">Ringkasan Produk</div>
    <table class="data-table" style="width: 100%; margin-bottom: 20px;">
        <thead>
            <tr style="background-color: #A435F0; color: white;">
                <th style="padding: 10px; text-align: left; border: 1px solid #A435F0;">Produk</th>
                <th style="padding: 10px; text-align: center; border: 1px solid #A435F0;">Harga</th>
                <th style="padding: 10px; text-align: center; border: 1px solid #A435F0;">Stok</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products ?? [] as $index => $product)
            <tr style="background-color: {{ $index % 2 === 0 ? '#FFFFFF' : '#F9F9F9' }}; border-bottom: 1px solid #E5E7EB;">
                <td style="padding: 10px; border: 1px solid #E5E7EB;">{{ $product->name }}</td>
                <td style="padding: 10px; text-align: center; border: 1px solid #E5E7EB;">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td style="padding: 10px; text-align: center; border: 1px solid #E5E7EB;">
                    <span style="padding: 4px 8px; background-color: {{ $product->stock > 5 ? '#D4EDDA' : ($product->stock > 0 ? '#FFF3CD' : '#F8D7DA') }}; color: {{ $product->stock > 5 ? '#155724' : ($product->stock > 0 ? '#856404' : '#721C24') }}; border-radius: 3px;">
                        {{ $product->stock }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="padding: 15px; text-align: center; color: #666;">Belum ada produk</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary Statistics -->
    <div style="display: flex; gap: 15px; margin-top: 20px;">
        <div style="flex: 1; padding: 15px; background-color: #F0E6FF; border-left: 4px solid #A435F0; border-radius: 4px;">
            <div style="font-size: 12px; color: #666; margin-bottom: 5px;">Total Produk</div>
            <div style="font-size: 24px; font-weight: bold; color: #A435F0;">{{ count($products ?? []) }}</div>
        </div>
        <div style="flex: 1; padding: 15px; background-color: #E6F2FF; border-left: 4px solid #2196F3; border-radius: 4px;">
            <div style="font-size: 12px; color: #666; margin-bottom: 5px;">Total Stok</div>
            <div style="font-size: 24px; font-weight: bold; color: #2196F3;">{{ $products?->sum('stock') ?? 0 }}</div>
        </div>
    </div>

    <!-- Notes -->
    <div style="margin-top: 30px; padding: 15px; background-color: #FEF9E7; border: 1px solid #F5E6C8; border-radius: 4px;">
        <div style="font-weight: 600; color: #856404; margin-bottom: 8px;">📋 Catatan Penting</div>
        <ul style="margin: 0; padding-left: 20px; color: #666; font-size: 12px;">
            <li>Pantau stok produk Anda secara berkala</li>
            <li>Produk dengan stok kurang dari 2 unit perlu segera di-restock</li>
            <li>Tingkatkan rating produk dengan memberikan layanan terbaik</li>
            <li>Update informasi toko secara rutin untuk kepercayaan pembeli</li>
        </ul>
    </div>
</div>
@endsection
