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

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="gambar">Gambar Barang</label>
                        <div class="col-sm-10">
                            <input type="file" name="gambar[]" id="gambar" multiple accept="image/*" onchange="previewImages()">
                        </div>
                    </div>
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
        let selectedFiles = new DataTransfer();

        function previewImages() {
            var preview = document.getElementById('imagePreview');
            var input = document.getElementById('gambar');
            var files = input.files;

            for (let i = 0; i < files.length; i++) {
                let file = files[i];
                selectedFiles.items.add(file);

                var reader = new FileReader();
                reader.onload = function(e) {
                    var container = document.createElement('div');
                    container.classList.add('image-container');

                    var img = document.createElement('img');
                    img.src = e.target.result;
                    container.appendChild(img);

                    var deleteButton = document.createElement('button');
                    deleteButton.textContent = '×';
                    deleteButton.classList.add('delete-image-btn');

                    // Event untuk menghapus gambar pratinjau
                    deleteButton.onclick = function() {
                        for (let j = 0; j < selectedFiles.items.length; j++) {
                            if (file.name === selectedFiles.items[j].getAsFile().name) {
                                selectedFiles.items.remove(j);
                                break;
                            }
                        }

                        input.files = selectedFiles.files;
                        container.remove();
                    };

                    container.appendChild(deleteButton);
                    preview.appendChild(container);
                };

                reader.readAsDataURL(file);
            }

            input.files = selectedFiles.files;
        }

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
