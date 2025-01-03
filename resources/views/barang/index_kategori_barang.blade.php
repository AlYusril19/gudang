@extends('layouts.app_sneat')

@section('content')
    {{-- <h5 class="pb-1 mb-6">Data Peserta</h5> --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Kategori</h5>
            <a href="{{ route('kategori.create') }}" class="btn btn-primary mb-0">Tambah Kategori</a>
        </div>
        <div class="text-nowrap table-responsive">
            <table class="table">
                <caption class="ms-4">
                    Data Kategori
                </caption>
                <thead>
                    <tr align="center">
                        <th width="5%">No</th>
                        <th>Kategori</th>
                        <th>satuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($kategoris as $kategori)
                        <tr align="center">
                            <td><i class="fab fa-angular fa-lg text-danger"></i> <strong>{{ $loop->iteration }}</strong></td>
                            <td>{{ $kategori->nama_kategori }}</td>
                            <td>{{ $kategori->satuan ?? '-' }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        {{-- <a class="dropdown-item" href="{{ route('users.show', $user->id) }}"><i class="bx bx-show-alt me-2"></i> Show</a> --}}
                                        <a class="dropdown-item" href="{{ route('kategori.edit', $kategori->id) }}"><i class="bx bx-edit-alt me-2"></i> Edit</a>
                                        <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Kategori ini?');">
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
        </div>
    </div>
    <hr class="my-12">
@endsection
