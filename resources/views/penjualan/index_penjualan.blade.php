@extends('layouts.app_sneat')

@section('content')
    <div class="card">
        {{-- <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Barang Keluar</h5>
            <a href="{{ route('penjualan.create') }}" class="btn btn-primary mb-0">Barang Keluar</a>
        </div> --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <a href="{{ route('penjualan.create') }}" class="btn btn-primary mb-0">Add Penjualan</a>
            {{-- <div class=" align-items-center"> --}}
                <form action="{{ route('penjualan.index') }}" method="GET" class="d-flex me-2">
                    <input type="hidden" name="search" class="form-control me-2" placeholder="Cari Customer / Bulan" value="{{ request('search') }}">
                    {{-- <button type="submit" class="btn btn-primary">Cari</button> --}}
                </form>
            {{-- </div> --}}
        </div>
        <div class="text-nowrap table-responsive me-2 ms-2">
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
                        <th>Aksi</th>
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
                            <td align="center">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <form action="{{ route('penjualan.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item"><i class="bx bx-trash me-1"></i> Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{-- {{ $barangs->links() }} --}}
        </div>
    </div>
    <hr class="my-12">
@endsection
