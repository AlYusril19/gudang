@extends('layouts.app_sneat')

<style>
    .image-container {
        position: relative;
        display: inline-block;
        margin: 10px;
    }

    .image-container img {
        width: 150px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .delete-image-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(255, 0, 0, 0.7);
        color: white;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        font-size: 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .delete-image-btn:hover {
        background: rgba(255, 0, 0, 1);
    }

    #imagePreview {
        display: flex;
        flex-wrap: wrap;
    }
</style>
<style>
    .image-container {
        position: relative; /* Agar elemen anak bisa diposisikan secara absolut */
    }

    .delete-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(255, 0, 0, 0.7);
        color: white;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        font-size: 18px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
</style>

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
                            <input type="number" class="form-control" id="harga_beli" name="harga_beli" value="{{ $barang->harga_beli }}" min="100" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="persen_laba">Persen Laba</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="number" class="form-control" id="persen_laba" name="persen_laba" value="{{ $persenJual }}" min="1" required>
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
                        <label class="col-sm-2 col-form-label" for="stok_minimal">Stok Minimal</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="number" class="form-control" id="stok_minimal" name="stok_minimal" value="{{ $barang->stok_minimal }}" required>
                            </div>
                        </div>
                    </div>

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

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="gambar">Upload Gambar <br><span class="text-muted">(Max: 5MB)</span></label>
                        <div class="col-sm-10">
                            <input type="file" name="gambar[]" id="gambar" multiple accept="image/*" onchange="previewAndCompressImages()">
                            {{-- <div id="imagePreview" class="d-flex flex-wrap"></div> --}}
                        </div>
                    </div>
                    <div id="imagePreview" class="image-preview"></div>
                    <div id="imagePreview" class="image-preview">
                        @foreach ($barang->galeri as $foto)
                            <div class="image-container">
                                <img src="{{ asset('storage/' . $foto->file_path) }}" alt="Barang" width="150px">
                                <button class="delete-btn" type="button" onclick="deleteImage({{ $foto->id }})">×</button>
                            </div>
                        @endforeach
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
@endsection

@section('js')
    {{-- Penjualan Function --}}
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

    {{-- Preview Image --}}
    <script>
        // Inisialisasi DataTransfer untuk menyimpan file yang dipilih
        let selectedFiles = new DataTransfer();

        // Fungsi untuk mempratinjau dan kompres gambar
        function previewAndCompressImages() {
            var preview = document.getElementById('imagePreview');
            var input = document.getElementById('gambar');
            var files = input.files;

            // Loop melalui file yang baru dipilih
            for (let i = 0; i < files.length; i++) {
                let file = files[i];

                // Jika ukuran file di bawah 350KB, tambahkan langsung tanpa kompresi
                if (file.size <= 350 * 1024) {
                    addFileToPreviewAndSelected(file);
                } else {
                    // Buat FileReader untuk membaca file
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var img = new Image();
                        img.src = e.target.result;

                        img.onload = function() {
                            var canvas = document.createElement('canvas');
                            var ctx = canvas.getContext('2d');

                            var width = img.width;
                            var height = img.height;
                            var maxDimension = 1024;

                            // Resize gambar jika lebih besar dari maxDimension
                            if (width > height) {
                                if (width > maxDimension) {
                                    height = Math.round((height *= maxDimension / width));
                                    width = maxDimension;
                                }
                            } else {
                                if (height > maxDimension) {
                                    width = Math.round((width *= maxDimension / height));
                                    height = maxDimension;
                                }
                            }

                            // Atur ukuran canvas
                            canvas.width = width;
                            canvas.height = height;
                            ctx.drawImage(img, 0, 0, width, height);

                            // Fungsi untuk kompresi bertahap hingga ukuran di bawah 350kB
                            function compressImage(quality, callback) {
                                canvas.toBlob(function(blob) {
                                    if (blob.size <= 350 * 1024 || quality <= 0.1) {
                                        var compressedFile = new File([blob], file.name, { type: file.type });
                                        callback(compressedFile);
                                    } else {
                                        compressImage(quality - 0.05, callback);
                                    }
                                }, file.type, quality);
                            }

                            // Kompres gambar dengan kualitas awal 0.9
                            compressImage(0.9, function(compressedFile) {
                                addFileToPreviewAndSelected(compressedFile);
                            });
                        };
                    };

                    reader.readAsDataURL(file);
                }
            }
        }

        // Fungsi untuk menambahkan file ke pratinjau dan DataTransfer
        function addFileToPreviewAndSelected(file) {
            // Tambahkan file ke DataTransfer
            selectedFiles.items.add(file);

            // Update input files dengan gambar yang terkompres
            document.getElementById('gambar').files = selectedFiles.files;

            // Buat pratinjau gambar
            var preview = document.getElementById('imagePreview');
            var container = document.createElement('div');
            container.classList.add('image-container');

            var img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            container.appendChild(img);

            var deleteButton = document.createElement('button');
            deleteButton.textContent = '×';
            deleteButton.classList.add('delete-image-btn');

            // Event untuk menghapus gambar
            deleteButton.onclick = function() {
                // Hapus file dari DataTransfer
                for (let j = 0; j < selectedFiles.items.length; j++) {
                    if (file.name === selectedFiles.items[j].getAsFile().name) {
                        selectedFiles.items.remove(j);
                        break;
                    }
                }

                // Update input files
                document.getElementById('gambar').files = selectedFiles.files;

                // Hapus pratinjau gambar
                container.remove();
            };

            container.appendChild(deleteButton);
            preview.appendChild(container);
        }
    </script>

    {{-- Delete Image --}}
    <script>
        // Fungsi untuk menghapus gambar yang sudah ada di database
        function deleteImage(imageId) {
            fetch(`/delete-image/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',  // Token CSRF untuk keamanan
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hapus elemen gambar dari DOM
                    document.querySelector(`button[onclick="deleteImage(${imageId})"]`).parentElement.remove();
                } else {
                    alert('Gagal menghapus gambar: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
@endsection
