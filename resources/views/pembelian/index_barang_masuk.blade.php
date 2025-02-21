@extends('layouts.app_sneat')

@section('content')
    @include('pembelian.navbar_index_pembelian')
    <div class="card">
        <div class="text-nowrap table-responsive ms-2 me-2 mt-2">
            <table class="table table-striped table-sm">
                <caption class="ms-4">
                    Data Barang Masuk
                </caption>
                <thead>
                    <tr align="center">
                        <th>No</th>
                        <th>Tanggal Pembelian</th>
                        <th>Supp | Usr</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Total Harga</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($pembelians as $p)
                        <tr>
                            <td align="center"><i class="fab fa-angular fa-lg text-danger"></i> <strong>{{ $loop->iteration }}</strong></td>
                            <td align="center">{{ $p->tanggal_pembelian }}</td>
                            <td>{{ $p->user->name ?? '-' }}</td>
                            <td>
                                @foreach ($p->pembelianBarang as $barang)
                                    {{ $barang->barang->nama_barang }} ({{ $barang->jumlah }} pcs)<br>
                                @endforeach
                            </td>
                            <td align="right">
                                @foreach ($p->pembelianBarang as $barang)
                                    {{ formatRupiah($barang->harga_beli * $barang->jumlah) }}<br>
                                @endforeach
                            </td>
                            <td align="right">{{ formatRupiah($p->total_harga) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
