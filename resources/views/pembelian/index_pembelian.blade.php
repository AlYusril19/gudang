@extends('layouts.app_sneat')

@section('content')
    @include('pembelian.navbar_index_pembelian')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <a href="{{ route('pembelian.create') }}" class="btn btn-primary mb-0">Add Pembelian</a>
            {{-- <div class=" align-items-center"> --}}
                <form action="{{ route('pembelian.index') }}" method="GET" class="d-flex me-2">
                    <input type="hidden" name="search" class="form-control me-2" placeholder="Cari Supplier / Kegiatan" value="{{ request('search') }}">
                    {{-- <button type="submit" class="btn btn-primary">Cari</button> --}}
                </form>
            {{-- </div> --}}
        </div>
        <div class="text-nowrap table-responsive">
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
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($pembelians as $p)
                        <tr>
                            <td align="center"><i class="fab fa-angular fa-lg text-danger"></i> <strong>{{ $loop->iteration }}</strong></td>
                            <td align="center">{{ $p->tanggal_pembelian }}</td>
                            <td>{{ $p->supplier->nama ?? '-' }} | {{ $p->user->name ?? '-' }}</td>
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
                            <td align="center">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('pembelian.edit', $p->id) }}"><i class="bx bx-edit-alt me-2"></i> Edit</a>
                                        <form action="{{ route('pembelian.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
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
        </div>
    </div>
@endsection
