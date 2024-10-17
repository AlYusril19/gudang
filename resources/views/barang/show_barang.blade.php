@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6 mb-4 order-0">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                    <a href="{{ route('barang.index') }}" class="btn btn-primary"> <i class="bx bx-left-arrow-alt me-0"></i>  Back</a>
                    <h5 class="mb-0">Detail Barang</h5>
                </div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="status">Status Barang</label>
                    {{-- <input type="text" class="form-control" id="status" value="{{ $barang->status }}" readonly> --}}
                    <span class="form-control badge {{ $barang->status == 'aktif' ? 'bg-label-success' : 'bg-label-warning' }}">{{ $barang->status }}</span>
                </div>
                <div class="form-group mb-3">
                    <label for="nama_kategori">Kategori Barang</label>
                    <input type="text" class="form-control" id="nama_kategori" value="{{ $barang->kategori->nama_kategori ?? '-'}}" readonly>
                </div>
                <div class="form-group mb-3">
                    <label for="kode_barang">Kode Barang</label>
                    <input type="text" class="form-control" id="kode_barang" value="{{ $barang->kode_barang }}" readonly>
                </div>
                <div class="form-group mb-3">
                    <label for="nama_barang">Nama Barang</label>
                    <input type="text" class="form-control" id="nama_barang" value="{{ $barang->nama_barang }}" readonly>
                </div>
                <div class="form-group mb-3">
                    <label for="harga_beli">Harga Beli</label>
                    <input type="text" class="form-control" id="harga_beli" value="{{ formatRupiah($barang->harga_beli) }}" readonly>
                </div>
                <div class="form-group mb-3">
                    <label for="harga_jual">Harga Jual</label>
                    <input type="text" class="form-control" id="harga_jual" value="{{ formatRupiah($barang->harga_jual) }}" readonly>
                </div>
                <div class="form-group mb-3">
                    <label for="stok">Stok Barang</label>
                    <input type="text" class="form-control" id="stok" value="{{ $barang->stok }}" readonly>
                </div>
                <div class="form-group mb-3">
                    <label for="deskripsi">Deskripsi Barang</label>
                    <input type="text" class="form-control" id="deskripsi" value="{{ $barang->deskripsi }}" readonly>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection