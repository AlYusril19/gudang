@extends('layouts.app_sneat')

@section('content')
<form action="{{ route('pembelian.store') }}" method="POST" id="form-pembelian">
    @csrf
    <!-- Supplier Selection -->
    <div class="form-group">
        <label for="supplier_id">Supplier</label>
        <select name="supplier_id" id="supplier_id" class="form-control">
            @foreach($supplier as $s)
                <option value="{{ $s->id }}">{{ $s->nama }}</option>
            @endforeach
        </select>
    </div>

    <!-- Barang Selection -->
    <div class="form-group">
        <label for="barang_id">Barang</label>
        <select name="barang_id" id="barang_id" class="form-control">
            @foreach($barang as $b)
                <option value="{{ $b->id }}">{{ $b->nama_barang }}</option>
            @endforeach
        </select>
    </div>

    <!-- Jumlah Input -->
    <div class="form-group">
        <label for="jumlah">Jumlah</label>
        <input type="number" name="jumlah" id="jumlah" class="form-control" value="1" min="1">
    </div>

    <!-- Button to Add Barang -->
    <button type="button" id="btn-tambah-barang" class="btn btn-primary">Tambah Barang</button>

    <!-- Table of Added Barang -->
    <table class="table table-bordered" id="daftar-barang">
        <thead>
            <tr>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <!-- Submit Button -->
    <button type="submit" id="btn-submit-pembelian" class="btn btn-success">Simpan Pembelian</button>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        // Add Barang to List
        $('#btn-tambah-barang').on('click', function() {
            var barangId = $('#barang_id').val();
            var barangNama = $('#barang_id option:selected').text();
            var jumlah = $('#jumlah').val();

            if (barangId && jumlah > 0) {
                var exists = false;
                $('#daftar-barang tr').each(function() {
                    if ($(this).data('barang-id') == barangId) {
                        exists = true;
                        return false;
                    }
                });

                if (!exists) {
                    var row = `<tr data-barang-id="${barangId}">
                        <td>${barangNama}</td>
                        <td>${jumlah}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-hapus-barang">Hapus</button>
                        </td>
                        <input type="hidden" name="barang_ids[]" value="${barangId}">
                        <input type="hidden" name="jumlah[]" value="${jumlah}">
                    </tr>`;
                    $('#daftar-barang tbody').append(row);

                    // Reset Inputs
                    $('#barang_id').val('');
                    $('#jumlah').val(1);
                } else {
                    alert('Barang sudah ditambahkan!');
                }
            } else {
                alert('Pilih barang dan masukkan jumlah yang valid!');
            }
        });

        // Remove Barang from List
        $(document).on('click', '.btn-hapus-barang', function() {
            $(this).closest('tr').remove();
        });

        // Submit Form Pembelian
        $('#btn-submit-pembelian').on('click', function() {
            if ($('#daftar-barang tbody tr').length > 0) {
                $('#form-pembelian').submit();
            } else {
                alert('Tambahkan minimal satu barang!');
            }
        });
    });
</script>
@endsection
