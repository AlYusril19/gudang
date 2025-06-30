@extends('layouts.app_sneat')

@section('content')
    
    @include('admin.users-navbar-index')
    {{-- <h5 class="pb-1 mb-6">Data Peserta</h5> --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Akun</h5>
            <a href="{{ route('users.create') }}" class="btn btn-primary mb-0">Tambah Akun</a>
        </div>
        <div class="text-nowrap table-responsive">
            <table class="table">
                <caption class="ms-4">
                    Data Akun Admin
                </caption>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($users as $user)
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{ $loop->iteration }}</strong></td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                            <td>
                                <input type="checkbox" class="toggle-status" data-id="{{ $user->id }}" {{ $user->status == 'aktif' ? 'checked' : '' }}>
                                <span class="status-label">{{ $user->status == 'aktif' ? 'Aktif' : 'Arsip' }}</span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        {{-- <a class="dropdown-item" href="{{ route('users.show', $user->id) }}"><i class="bx bx-show-alt me-2"></i> Show</a> --}}
                                        {{-- <a class="dropdown-item" href="{{ route('users.edit', $user->id) }}"><i class="bx bx-edit-alt me-2"></i> Edit</a> --}}
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus peserta ini?');">
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
            {{-- {{ $users->links() }} --}}
        </div>
    </div>
    {{-- <hr class="my-12"> --}}
@endsection

@section('js')
{{-- status aktif dan arsip user --}}
    <script>
        $(document).on('change', '.toggle-status', function() {
            var id = $(this).data('id');
            var status = $(this).is(':checked') ? 'aktif' : 'arsip';

            $.ajax({
                url: '{{ route('user.toggleStatus') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        // Update status label text without refreshing
                        $('#user-' + id).find('.status-label').text(status == 'aktif' ? 'Aktif' : 'Arsip');
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