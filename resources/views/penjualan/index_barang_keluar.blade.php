@extends('layouts.app_sneat')

@section('content')
    <div class="card">
        <div class="text-nowrap table-responsive ms-2 me-2 mt-2">
            <table class="table table-striped table-sm">
                <caption class="ms-4">
                    Data Barang Keluar
                </caption>
                <thead>
                    <tr align="center">
                        <th>No</th>
                        <th>Tanggal Penjualan</th>
                        <th>Cust / Usr</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Total Harga</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($penjualan as $p)
                        <tr>
                            <td align="center"><i class="fab fa-angular fa-lg text-danger"></i> <strong>{{ $loop->iteration }}</strong></td>
                            <td align="center">{{ $p->tanggal_penjualan }}</td>
                            <td>{{ $p->customer->nama ?? '-' }} | {{ $p->user->name ?? '-' }}</td>
                            <td>
                                @foreach ($p->penjualanBarang as $barang)
                                    {{ $barang->barang->nama_barang }} ({{ $barang->jumlah }} pcs)<br>
                                @endforeach
                            </td>
                            <td align="right">
                                @foreach ($p->penjualanBarang as $barang)
                                    {{ formatRupiah($barang->harga_jual * $barang->jumlah) }}<br>
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
