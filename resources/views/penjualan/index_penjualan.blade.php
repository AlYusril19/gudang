@extends('layouts.app_sneat')

@section('content')
    {{-- <h5 class="pb-1 mb-6">Data Peserta</h5> --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Barang Keluar</h5>
            <a href="{{ route('penjualan.create') }}" class="btn btn-primary mb-0">Barang Keluar</a>
        </div>
        <div class="text-nowrap table-responsive">
            <table class="table">
                <caption class="ms-4">
                    Data Barang Keluar
                </caption>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Harga Jual</th>
                        <th>Jumlah</th>
                        <th>Tanggal Penjualan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($penjualans as $penjualan)
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{ $loop->iteration }}</strong></td>
                            <td>{{ $penjualan->barang->nama_barang }}</td>
                            <td>{{ formatRupiah($penjualan->harga_jual) }}</td>
                            <td>{{ $penjualan->jumlah }}</td>
                            <td>{{ $penjualan->tanggal_penjualan->format('Y-m-d') }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        {{-- <a class="dropdown-item" href="{{ route('barang.show', $barang->id) }}"><i class="bx bx-show-alt me-2"></i> Show</a> --}}
                                        {{-- <a class="dropdown-item" href="{{ route('pembelian.edit', $pembelian->id) }}"><i class="bx bx-edit-alt me-2"></i> Edit</a> --}}
                                        <form action="{{ route('penjualan.destroy', $penjualan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus peserta ini?');">
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
