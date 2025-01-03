@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Input Kategori</h5>
                    <small class="text-muted float-end">Form Kategori</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('kategori.update', $kategori->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="nama_kategori">Nama Kategori</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" placeholder="Router" value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="satuan">Satuan</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="satuan" name="satuan" placeholder="pcs" value="{{ old('satuan', $kategori->satuan) }}">
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
