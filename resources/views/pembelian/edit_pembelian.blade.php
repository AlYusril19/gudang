@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Edit Barang Masuk</h5>
                    <small class="text-muted float-end">Form Edit Barang Masuk</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('pembelian.update', $pembelian->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="barang_id">ID Barang</label>
                            <div class="col-sm-10">
                                <select name="barang_id" id="barang_id" class="form-control select2">
                                    @foreach($barang as $b)
                                        <option value="{{ $b->id }}" {{ $pembelian->barang_id == $b->id ? 'selected' : '' }}>
                                            {{ $b->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="jumlah">Jumlah</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="jumlah" name="jumlah" value="{{ $pembelian->jumlah }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="harga_beli">Harga Beli</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="harga_beli" name="harga_beli" value="{{ $pembelian->harga_beli }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="tanggal_pembelian">Tanggal Pembelian</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="tanggal_pembelian" name="tanggal_pembelian" value="{{ $pembelian->tanggal_pembelian->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Simpan</button>
                            <button type="reset" class="btn btn-outline-secondary">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
