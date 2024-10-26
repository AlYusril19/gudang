@extends('layouts.app_sneat')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('penjualan.store') }}" method="POST" id="form-penjualan">
            @csrf
            <!-- Customer Selection -->
            <div class="form-group">
                <label for="customer_id">Customer</label>
                <select name="customer_id" id="customer_id" class="form-control">
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->nama }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Barang Selection -->
            <div class="form-group mt-2">
                <label for="barang_id">Barang</label>
                <select name="barang_id" id="barang_id" class="form-control select2">
                    <option value="">Pilih Barang</option>
                    @foreach($barang as $b)
                        <option value="{{ $b->id }}" data-harga="{{ $b->harga_jual }}">{{ $b->nama_barang }} | {{ formatRupiah($b->harga_jual) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Display Available Stock -->
            <div class="form-group mt-2">
                <label for="stok">Stok Tersedia</label>
                <input type="number" id="stok" class="form-control" disabled>
            </div>

            <!-- Jumlah Input -->
            <div class="form-group mt-2">
                <label for="jumlah">Jumlah</label>
                <input type="number" name="jumlah" id="jumlah" class="form-control" value="1" min="1">
            </div>

            <!-- Button to Add Barang -->
            <button type="button" id="btn-tambah-barang" class="btn btn-primary mt-2">Tambah Barang</button>

            <!-- Table of Added Barang -->
            <table class="table table-bordered mt-3" id="daftar-barang">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Harga Jual</th>
                        <th>Total Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <!-- Total Harga Jual -->
            <div class="form-group">
                <label>Total Harga Jual</label>
                <input type="text" id="total-harga-jual" class="form-control" readonly>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="btn-submit-penjualan" class="btn btn-success mt-2">Simpan Penjualan</button>
        </form>
    </div>
</div>

@endsection

@section('js')
    <!-- AJAX Code -->
<script>
    $(document).ready(function() {
        function updateTotalHargaJual() {
            let totalHarga = 0;

            $('#daftar-barang tbody tr').each(function() {
                const jumlah = parseInt($(this).find('.jumlah-barang').text());
                const hargaJual = parseFloat($(this).find('.barang-harga-jual').text().replace(/[^0-9]/g, '')); // Remove 'Rp ' and format to number
                const total = jumlah * hargaJual;

                $(this).find('.total-harga').text(formatRupiahJS(total));
                totalHarga += total;
            });

            $('#total-harga-jual').val(formatRupiahJS(totalHarga));
        }

        // Function to handle barang selection and fetch stock
        $('#barang_id').on('change', function() {
            var barangId = $(this).val();

            if (barangId) {
                // AJAX request to get stock for the selected barang
                $.ajax({
                    url: '/get-stok/' + barangId, // URL untuk mengambil stok barang
                    method: 'GET',
                    success: function(data) {
                        if (data.stok !== undefined) {
                            $('#stok').val(data.stok); // Set stok value ke field stok
                        }
                    },
                    error: function() {
                        alert('Gagal mendapatkan stok barang!');
                    }
                });
            } else {
                $('#stok').val(''); // Reset stok jika tidak ada barang dipilih
            }
        });

        // Add Barang to the list
        $('#btn-tambah-barang').on('click', function() {
            var barangId = $('#barang_id').val();
            var barangNama = $('#barang_id option:selected').text();
            var hargaJual = parseFloat($('#barang_id option:selected').data('harga'));
            var jumlah = parseInt($('#jumlah').val());
            var stok = parseInt($('#stok').val());

            if (barangId && jumlah > 0 && jumlah <= stok) {
                // Cek apakah barang sudah ditambahkan
                var exists = false;
                $('#daftar-barang tr').each(function() {
                    if ($(this).data('barang-id') == barangId) {
                        exists = true;
                        var currentJumlah = parseInt($(this).find('.jumlah-barang').text());
                        var newJumlah = currentJumlah + jumlah;

                        var maxStok = parseInt($(this).data('stok-barang'));

                        if (newJumlah <= maxStok) {
                            $(this).find('.jumlah-barang').text(newJumlah);
                            $(this).find('input[name="jumlah[]"]').val(newJumlah);
                            updateTotalHargaJual();
                        } else {
                            alert('Jumlah total melebihi stok yang tersedia!');
                        }
                        return false;
                    }
                });

                if (!exists) {
                    var totalHarga = hargaJual * jumlah;
                    var row = `<tr data-barang-id="${barangId}" data-stok-barang="${stok}">
                        <td>${barangNama}</td>
                        <td>
                            <button type="button" class="btn btn-secondary btn-kurang-barang">-</button>
                            <span class="jumlah-barang">${jumlah}</span>
                            <button type="button" class="btn btn-secondary btn-tambah-barang">+</button>
                            <input type="hidden" name="barang_ids[]" value="${barangId}">
                            <input type="hidden" name="jumlah[]" value="${jumlah}">
                        </td>
                        <td>
                            <span class="barang-harga-jual">${formatRupiahJS(hargaJual)}</span>
                        </td>
                        <td class="total-harga">${formatRupiahJS(totalHarga)}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-hapus-barang">Hapus</button>
                        </td>
                    </tr>`;
                    $('#daftar-barang tbody').append(row);

                    // Reset Inputs
                    $('#barang_id').val('');
                    $('#jumlah').val(1);
                    $('#stok').val('');

                    updateTotalHargaJual();
                }
            } else {
                alert('Jumlah yang dimasukkan melebihi stok yang tersedia!');
            }
        });

        // Function to Remove Barang
        $(document).on('click', '.btn-hapus-barang', function() {
            $(this).closest('tr').remove();
            updateTotalHargaJual();
        });

        // Function to increase quantity
        $(document).on('click', '.btn-tambah-barang', function() {
            var row = $(this).closest('tr');
            var currentJumlah = parseInt(row.find('.jumlah-barang').text());
            var maxStok = parseInt(row.data('stok-barang'));

            if (currentJumlah < maxStok) {
                var newJumlah = currentJumlah + 1;
                row.find('.jumlah-barang').text(newJumlah);
                row.find('input[name="jumlah[]"]').val(newJumlah);
                row.find('td.total-harga').text(formatRupiahJS(newJumlah * parseFloat(row.find('.barang-harga-jual').text().replace(/[^0-9]/g, ''))));
                updateTotalHargaJual();
            } else {
                alert('Jumlah melebihi stok yang tersedia!');
            }
        });

        // Function to decrease quantity
        $(document).on('click', '.btn-kurang-barang', function() {
            var row = $(this).closest('tr');
            var currentJumlah = parseInt(row.find('.jumlah-barang').text());

            if (currentJumlah > 1) {
                var newJumlah = currentJumlah - 1;
                row.find('.jumlah-barang').text(newJumlah);
                row.find('input[name="jumlah[]"]').val(newJumlah);
                row.find('td.total-harga').text(formatRupiahJS(newJumlah * parseFloat(row.find('.barang-harga-jual').text().replace(/[^0-9]/g, ''))));
                updateTotalHargaJual();
            } else {
                alert('Jumlah tidak bisa kurang dari 1!');
            }
        });

        // Function to handle form submission
        $('#btn-submit-penjualan').on('click', function() {
            var form = $('#form-penjualan');
            if ($('#daftar-barang tbody tr').length > 0) {
                form.submit();
            } else {
                alert('Tambahkan minimal satu barang sebelum menyimpan penjualan!');
            }
        });
    });
</script>
@endsection