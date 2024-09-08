@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Input Barang Keluar</h5>
                    <small class="text-muted float-end">Form Data Barang Keluar</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('penjualan.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="barang_id">ID Barang</label>
                            <div class="col-sm-10">
                                <select name="barang_id" id="barang_id" class="form-control select2">
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach($barang as $b)
                                        <option value="{{ $b->id }}">{{ $b->nama_barang }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="jumlah">Jumlah</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="jumlah" name="jumlah" placeholder="10" required>
                            </div>
                        </div>
                        {{-- <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="harga_jual">Harga Jual</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="harga_jual" name="harga_jual" readonly>
                            </div>
                        </div> --}}
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="total_harga">Harga</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="total_harga" name="total_harga" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label" for="tanggal_penjualan">Tanggal Penjualan</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="tanggal_penjualan" name="tanggal_penjualan" required>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#barang_id, #jumlah').on('change input', function() {
                var barangId = $('#barang_id').val();
                var jumlah = $('#jumlah').val();

                // Jika barang dan jumlah sudah diisi
                if (barangId && jumlah) {
                    $.ajax({
                        url: '/get-harga-jual', 
                        type: 'POST',  // Ubah menjadi POST
                        data: {
                            _token: '{{ csrf_token() }}',  // Sertakan CSRF token
                            barang_id: barangId,
                            jumlah: jumlah
                        },
                        success: function(data) {
                            if (data.total_harga) {
                                $('#total_harga').val(data.total_harga);
                            }
                        }
                    });
                } else {
                    $('#total_harga').val('');
                }
            });
        });
    </script>
@endsection
