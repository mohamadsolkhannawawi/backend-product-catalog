@extends('pdf.layouts.formal-report', [
    'reportTitle' => $reportTitle ?? 'LAPORAN ULASAN DAN RATING',
    'reportDate' => $reportDate ?? now()->format('d-m-Y'),
])

@section('content')
<table>
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 20%;">Produk</th>
            <th style="width: 15%;">Peninjau</th>
            <th style="width: 15%;">Provinsi</th>
            <th style="width: 8%;">Rating</th>
            <th style="width: 20%;">Komentar</th>
            <th style="width: 12%;">Tanggal</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data ?? [] as $index => $review)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td>{{ $review['product_name'] ?? '-' }}</td>
            <td>
                <small>
                    {{ $review['reviewer_name'] ?? '-' }}<br>
                    {{ $review['reviewer_email'] ?? '-' }}
                </small>
            </td>
            <td>{{ $review['province_name'] ?? '-' }}</td>
            <td class="text-center">
                <strong>{{ $review['rating'] ?? 0 }}/5</strong>
            </td>
            <td>
                <small>{{ substr($review['comment'] ?? '', 0, 50) }}{{ strlen($review['comment'] ?? '') > 50 ? '...' : '' }}</small>
            </td>
            <td class="text-center">
                <small>{{ \Carbon\Carbon::parse($review['created_at'])->format('d-m-Y') }}</small>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center">Tidak ada ulasan</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top: 20pt; font-size: 9pt; color: #666;">
    <p><strong>Total Ulasan:</strong> {{ count($data ?? []) }}</p>
    <p><strong>Rata-rata Rating:</strong> {{ collect($data ?? [])->avg('rating') ? number_format(collect($data ?? [])->avg('rating'), 1) : 'N/A' }}/5</p>
    <p><strong>Rating 5 Bintang:</strong> {{ collect($data ?? [])->where('rating', 5)->count() }}</p>
    <p><strong>Rating 4 Bintang:</strong> {{ collect($data ?? [])->where('rating', 4)->count() }}</p>
    <p><strong>Rating 3 Bintang:</strong> {{ collect($data ?? [])->where('rating', 3)->count() }}</p>
    <p><strong>Rating < 3 Bintang:</strong> {{ collect($data ?? [])->where('rating', '<', 3)->count() }}</p>
</div>
@endsection
