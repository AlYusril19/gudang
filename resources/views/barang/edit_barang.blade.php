@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Edit Data Barang</h5>
                <small class="text-muted float-end">Form Edit Barang</small>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('barang.update', $barang->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="nama_barang">Kategori</label>
                        <div class="col-sm-10">
                            <select name="kategori_id" id="kategori_id" class="form-control select2">
                                <option value="">--Pilih Kategori--</option>
                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}" {{ $barang->kategori_id == $kategori->id ? 'selected' : '' }}>{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="nama_barang">Nama Barang</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="{{ $barang->nama_barang }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="harga_beli">Harga Beli</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="harga_beli" name="harga_beli" value="{{ $barang->harga_beli }}" min="500" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="persen_laba">Persen Laba</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="number" class="form-control" id="persen_laba" name="persen_laba" value="{{ $persenJual }}" min="1" max="100" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Input untuk menampilkan hasil hitungan harga jual -->
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="harga_jual_display">Harga Jual</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="harga_jual_display" readonly>
                        </div>
                    </div>

                    <!-- Hidden input untuk mengirim harga jual yang sebenarnya ke server -->
                    <input type="hidden" id="harga_jual" name="harga_jual">

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="deskripsi">Deskripsi</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="deskripsi" name="deskripsi" value="{{ $barang->deskripsi }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="status">Status</label>
                        <div class="col-sm-10">
                            <select name="status" id="status" class="form-control">
                                <option value="arsip" {{ $barang->status == 'arsip' ? 'selected' : '' }}>Arsip</option>
                                <option value="aktif" {{ $barang->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">Update</button>
                        <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Fungsi untuk menghitung harga jual
    function hitungHargaJual() {
        var hargaBeli = document.getElementById('harga_beli').value;
        var persenLaba = document.getElementById('persen_laba').value;

        if (hargaBeli && persenLaba) {
            var hargaJual = parseFloat(hargaBeli) + (parseFloat(hargaBeli) * (parseFloat(persenLaba) / 100));
            document.getElementById('harga_jual_display').value = formatRupiahJS(hargaJual.toString(), 'Rp ');
            document.getElementById('harga_jual').value = parseFloat(persenLaba);  // Set harga jual sebenarnya untuk dikirim ke server
        } else {
            document.getElementById('harga_jual_display').value = '';
            document.getElementById('harga_jual').value = '';
        }
    }

    // Panggil fungsi hitungHargaJual() saat halaman di-load
    document.addEventListener('DOMContentLoaded', function() {
        hitungHargaJual();
    });

    // Event listener untuk memantau perubahan di harga beli dan persen laba
    document.getElementById('harga_beli').addEventListener('input', hitungHargaJual);
    document.getElementById('persen_laba').addEventListener('input', hitungHargaJual);
</script>
@endsection
