@extends('layouts.app_sneat')

@section('content')
    {{-- <h5 class="pb-1 mb-6">Data Peserta</h5> --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <a href="{{ route('barang.create') }}" class="btn btn-primary mb-0">Tambah Item</a>
            <div class=" align-items-center">
                <form action="{{ route('barang.index') }}" method="GET" class="d-flex me-2">
                    <input type="text" name="search" class="form-control" placeholder="Cari Barang" value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </form>
            </div>
        </div>
        <div class="text-nowrap table-responsive">
            <table class="table">
                <caption class="ms-4">
                    Data Item
                </caption>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>
                            <a href="{{ route('barang.index', ['order_by' => 'nama_barang', 'direction' => $orderBy === 'nama_barang' && $direction === 'asc' ? 'desc' : 'asc']) }}">
                                Nama Barang
                                @if($orderBy === 'nama_barang')
                                    <i class="bx {{ $direction === 'asc' ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('barang.index', ['order_by' => 'harga_beli', 'direction' => $orderBy === 'harga_beli' && $direction === 'asc' ? 'desc' : 'asc']) }}">
                                Harga Beli
                                @if($orderBy === 'harga_beli')
                                    <i class="bx {{ $direction === 'asc' ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>Harga Jual</th>
                        <th>
                            <a href="{{ route('barang.index', ['order_by' => 'stok', 'direction' => $orderBy === 'stok' && $direction === 'asc' ? 'desc' : 'asc']) }}">
                                Stok
                                @if($orderBy === 'stok')
                                    <i class="bx {{ $direction === 'asc' ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($barangs as $barang)
                        <tr id="barang-{{ $barang->id }}">
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{ $loop->iteration }}</strong></td>
                            <td>{{ $barang->nama_barang }}</td>
                            <td>{{ formatRupiah($barang->harga_beli) }}</td>
                            <td>{{ formatRupiah($barang->harga_jual) }}</td>
                            <td>{{ $barang->stok }}</td>
                            <td>
                                <input type="checkbox" class="toggle-status" data-id="{{ $barang->id }}" {{ $barang->status == 'aktif' ? 'checked' : '' }}>
                                <span class="status-label">{{ $barang->status == 'aktif' ? 'Aktif' : 'Arsip' }}</span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('barang.show', $barang->id) }}"><i class="bx bx-show-alt me-2"></i> Show</a>
                                        <a class="dropdown-item" href="{{ route('barang.edit', $barang->id) }}"><i class="bx bx-edit-alt me-2"></i> Edit</a>
                                        <form action="{{ route('barang.destroy', $barang->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus peserta ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item"><i class="bx bx-trash me-1"></i> Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{-- {{ $barangs->links() }} --}}
        </div>
    </div>
    <hr class="my-12">
@endsection

@section('js')
    <script>
        $(document).on('change', '.toggle-status', function() {
            var id = $(this).data('id');
            var status = $(this).is(':checked') ? 'aktif' : 'arsip';

            $.ajax({
                url: '{{ route('barang.toggleStatus') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        // Update status label text without refreshing
                        $('#barang-' + id).find('.status-label').text(status == 'aktif' ? 'Aktif' : 'Arsip');
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat mengubah status.');
                }
            });
        });
    </script>
@endsection