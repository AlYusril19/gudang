@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Update Customer</h5>
                <small class="text-muted float-end">Form Update Data Customer</small>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('customer.update', $customer->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT') <!-- Menambahkan metode PUT -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="nama">Nama</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Budiono Siregar" value="{{ old('nama', $customer->nama) }}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="hp">HP</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="hp" name="hp" placeholder="085712345678" value="{{ old('hp', $customer->hp) }}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="mitra_id">User Mitra</label>
                        <div class="col-sm-10">
                            <select name="mitra_id" id="mitra_id" class="form-control">
                                <option value="">Pilih User</option>
                                @foreach($users as $user) <!-- Pastikan ini adalah daftar user yang benar -->
                                    <option value="{{ $user->id }}" {{ $user->id == $customer->mitra_id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">Update</button>
                        <button type="reset" class="btn btn-outline-secondary">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
