@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Input Data Barang</h5>
                    <small class="text-muted float-end">Form Data Barang</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('barang.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="nama_barang">Kategori</label>
                            <div class="col-sm-10">
                                <select name="kategori_id" id="kategori_id" class="form-control select2">
                                    <option value="">--Pilih Kategori--</option>
                                    @foreach($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="nama_barang">Nama Barang</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="nama_barang" name="nama_barang" placeholder="Tenda N301" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="harga_beli">Harga Beli</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="harga_beli" name="harga_beli" placeholder="135000" min="500" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="harga_jual">Persen Laba</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="number" class="form-control" id="harga_jual" name="harga_jual" placeholder="20 (persen)" min="1" max="100" required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="deskripsi">Deskripsi</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="deskripsi" name="deskripsi" placeholder="Deskripsi Barang" required>
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