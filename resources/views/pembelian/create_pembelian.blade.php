@extends('layouts.app_sneat')

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('pembelian.store') }}" method="POST" id="form-pembelian">
            @csrf
            <!-- Supplier Selection -->
            <div class="form-group">
                <label for="supplier_id">Supplier</label>
                <select name="supplier_id" id="supplier_id" class="form-control select2" onchange="handleSupplierChange()">
                    <option value="">--Pilih Supplier--</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                    @endforeach
                    <option value="other">Other</option> <!-- Opsi Other -->
                </select>
            </div>

            <!-- Barang Selection -->
            <div class="form-group mt-2">
                <label for="barang_id">Barang</label>
                <select name="barang_id" id="barang_id" class="form-control select2">
                    <option value="">--Pilih Barang--</option>
                    @foreach($barang as $b)
                        <option value="{{ $b->id }}">{{ $b->nama_barang }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Jumlah Input -->
            <div class="form-group mt-2">
                <label for="jumlah">Jumlah</label>
                <input type="number" name="jumlah" id="jumlah" class="form-control" value="1" min="1">
            </div>

            <!-- Harga Beli Input -->
            <div class="form-group mt-2">
                <label for="harga_beli">Harga Beli</label>
                <input type="number" name="harga_beli" id="harga_beli" class="form-control" placeholder="150000" min="1">
            </div>


            <!-- Button to Add Barang -->
            <button type="button" id="btn-tambah-barang" class="btn btn-primary mt-2">Tambah Barang</button>

            <!-- Table of Added Barang -->
            <div class="table table-responsive">
                <table class="table table-striped mt-3" id="daftar-barang">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Harga Beli</th>
                            <th>Total Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0"></tbody>
                </table>
            </div>

            <!-- Total Harga Jual -->
            <div class="form-group mt-2">
                <label>Total Harga Jual</label>
                <input type="text" id="total-harga-beli" class="form-control" readonly>
            </div>

            <!-- Submit Button -->
            <button type="submit" id="btn-submit-pembelian" class="btn btn-success mt-2">Simpan Pembelian</button>
        </form>

        <!-- Modal for Adding Supplier -->
        <div class="modal fade" id="modalSupplier" tabindex="-1" aria-labelledby="modalSupplierLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalSupplierLabel">Tambah Supplier Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form-tambah-supplier">
                            @csrf
                            <div class="form-group">
                                <label for="nama">Nama Supplier</label>
                                <input type="text" name="nama" id="nama" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}

@endsection

@section('js')
<script>
    // Function to handle supplier selection
    function handleSupplierChange() {
        var select = document.getElementById('supplier_id');
        if (select.value === 'other') {
            // Munculkan modal tambah supplier
            $('#modalSupplier').modal('show');
        }
    }
    $(document).ready(function() {
        // Function to fetch and update harga_beli based on selected barang
        $('#barang_id').on('change', function() {
            var barangId = $(this).val();
            
            if (barangId) {
                $.ajax({
                    url: '{{ route("pembelian.getHargaBeli") }}',  // Route untuk mengambil harga beli
                    method: 'GET',
                    data: {
                        id: barangId
                    },
                    success: function(response) {
                        $('#harga_beli').val(response.harga_beli);
                    },
                    error: function() {
                        alert('Gagal mengambil harga beli');
                    }
                });
            } else {
                $('#harga_beli').val('');
            }
        });

        // Helper function to format number as currency in Rupiah
        function formatRupiah(angka) {
            let rupiah = '';
            let angkarev = angka.toString().split('').reverse().join('');
            for(let i = 0; i < angkarev.length; i++)
                if(i % 3 === 0) rupiah += angkarev.substr(i, 3) + '.';
            return 'Rp ' + rupiah.split('', rupiah.length - 1).reverse().join('');
        }

        // Function to update total harga beli
        function updateTotalHargaBeli() {
            let totalHargaBeli = 0;

            $('#daftar-barang tbody tr').each(function() {
                const jumlah = parseInt($(this).find('.barang-jumlah').val());
                const hargaBeli = parseFloat($(this).find('.barang-harga-beli').val());
                const total = jumlah * hargaBeli;

                $(this).find('.total-harga-beli').text(formatRupiah(total));
                totalHargaBeli += total;
            });

            $('#total-harga-beli').val(formatRupiah(totalHargaBeli));
        }

        // Add Barang to List
        $('#btn-tambah-barang').on('click', function() {
            var barangId = $('#barang_id').val();
            var barangNama = $('#barang_id option:selected').text();
            var jumlah = $('#jumlah').val();
            var hargaBeli = parseFloat($('#harga_beli').val().replace(/[^0-9]/g, '')); // Remove currency format and convert to number

            if (barangId && jumlah > 0 && hargaBeli > 0) {
                var exists = false;
                var rowToUpdate;

                // Check if the item is already in the list
                $('#daftar-barang tr').each(function() {
                    if ($(this).data('barang-id') == barangId) {
                        exists = true;
                        rowToUpdate = $(this);
                        return false;
                    }
                });

                if (exists) {
                    // Update the existing row's quantity and harga_beli
                    var currentJumlah = parseInt(rowToUpdate.find('.barang-jumlah').val());
                    var newJumlah = currentJumlah + parseInt(jumlah);
                    rowToUpdate.find('.barang-jumlah').val(newJumlah);
                    rowToUpdate.find('span.jumlah-display').text(newJumlah);
                    rowToUpdate.find('span.harga-beli-display').text(formatRupiah(hargaBeli)); // Update harga beli with formatted value
                    rowToUpdate.find('.barang-harga-beli').val(hargaBeli);
                } else {
                    // Add new row for new item
                    var totalHargaBeli = jumlah * hargaBeli;
                    var row = `<tr data-barang-id="${barangId}">
                        <td>${barangNama}</td>
                        <td class="jumlah-container">
                            <button type="button" class="btn btn-sm btn-secondary btn-kurang-jumlah">-</button>
                            <span class="jumlah-display">${jumlah}</span>
                            <button type="button" class="btn btn-sm btn-secondary btn-tambah-jumlah">+</button>
                            <input type="hidden" name="barang_ids[]" value="${barangId}">
                            <input type="hidden" name="jumlah[]" class="barang-jumlah" value="${jumlah}">
                        </td>
                        <td>
                            <span class="harga-beli-display">${formatRupiah(hargaBeli)}</span>
                            <input type="hidden" name="harga_beli[]" class="barang-harga-beli" value="${hargaBeli}">
                        </td>
                        <td class="total-harga-beli">${formatRupiah(totalHargaBeli)}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger btn-hapus-barang">Hapus</button>
                        </td>
                    </tr>`;
                    $('#daftar-barang tbody').append(row);
                }

                // Reset the input fields
                $('#barang_id').val('');
                $('#jumlah').val(1);
                $('#harga_beli').val('');

                updateTotalHargaBeli(); // Update total harga beli after adding or updating item
            } else {
                alert('Pilih barang, masukkan jumlah, dan harga beli yang valid!');
            }
        });

        // Increase jumlah
        $(document).on('click', '.btn-tambah-jumlah', function() {
            var row = $(this).closest('tr');
            var jumlahInput = row.find('.barang-jumlah');
            var newJumlah = parseInt(jumlahInput.val()) + 1;
            jumlahInput.val(newJumlah);
            row.find('span.jumlah-display').text(newJumlah);

            var hargaBeli = parseFloat(row.find('.barang-harga-beli').val());
            row.find('td.total-harga-beli').text(formatRupiah((newJumlah * hargaBeli)));

            updateTotalHargaBeli(); // Update total harga beli after increasing quantity
        });

        // Decrease jumlah
        $(document).on('click', '.btn-kurang-jumlah', function() {
            var row = $(this).closest('tr');
            var jumlahInput = row.find('.barang-jumlah');
            var currentJumlah = parseInt(jumlahInput.val());
            if (currentJumlah > 1) {
                var newJumlah = currentJumlah - 1;
                jumlahInput.val(newJumlah);
                row.find('span.jumlah-display').text(newJumlah);

                var hargaBeli = parseFloat(row.find('.barang-harga-beli').val());
                row.find('td.total-harga-beli').text(formatRupiah((newJumlah * hargaBeli)));

                updateTotalHargaBeli(); // Update total harga beli after decreasing quantity
            } else {
                alert('Jumlah tidak bisa kurang dari 1!');
            }
        });

        // Remove Barang from List
        $(document).on('click', '.btn-hapus-barang', function() {
            $(this).closest('tr').remove();
            updateTotalHargaBeli(); // Update total harga beli after removing item
        });

        // Submit Form Pembelian
        $('#btn-submit-pembelian').on('click', function() {
            if ($('#daftar-barang tbody tr').length > 0) {
                $('#form-pembelian').submit();
            } else {
                alert('Tambahkan minimal satu barang!');
            }
        });

        // Handle submit supplier form
        $('#form-tambah-supplier').on('submit', function(e) {
            e.preventDefault();

            var namaSupplier = $('#nama').val();

            $.ajax({
                url: '{{ route("supplier.store") }}',  // Route untuk menyimpan supplier
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    nama: namaSupplier,
                },
                success: function(response) {
                    // Tambahkan supplier ke dropdown
                    var newOption = new Option(response.supplier.nama, response.supplier.id, false, true);
                    $('#supplier_id').append(newOption).trigger('change');

                    // Tutup modal
                    $('#modalSupplier').modal('hide');
                    $('#form-tambah-supplier')[0].reset(); // Reset form supplier
                },
                error: function() {
                    alert('Gagal menambahkan supplier');
                }
            });
        });
    });
</script>
@endsection