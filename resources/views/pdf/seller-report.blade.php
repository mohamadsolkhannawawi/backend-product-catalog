<h2>Laporan Seller</h2>

<p><strong>Nama Seller:</strong> {{ $seller->store_name }}</p>
<p><strong>Phone:</strong> {{ $seller->phone }}</p>
<p><strong>Total Produk:</strong> {{ $products->count() }}</p>

<hr>

<h3>Daftar Produk</h3>

<table width="100%" border="1" cellspacing="0" cellpadding="4">
    <thead>
        <tr>
            <th>Nama Produk</th>
            <th>Harga</th>
            <th>Stock</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $p)
            <tr>
                <td>{{ $p->name }}</td>
                <td>Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                <td>{{ $p->stock }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
