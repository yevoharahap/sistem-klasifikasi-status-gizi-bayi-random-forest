@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="card shadow-sm">

        {{-- ================= HEADER CARD ================= --}}
        <div class="card-body border-bottom pb-3 mb-3">
            <div class="row align-items-center">

                {{-- ===== JUDUL & DESKRIPSI ===== --}}
                <div class="col-md-8">
                    <h3 class="fw-bold text-dark mb-1">
                        Manajemen Data User
                    </h3>
                    <p class="text-muted mb-0">
                        Halaman ini menampilkan daftar pengguna sistem. Anda dapat menambah, mengedit, atau menghapus user
                        sesuai hak akses. Pastikan email dan password user tercatat dengan benar.
                    </p>
                </div>

                {{-- ===== BUTTON TAMBAH USER ===== --}}
                <div class="col-md-4 text-end mt-3 mt-md-0">
                    <a href="{{ route('user.create') }}" class="btn btn-primary shadow-sm px-4">
                        <i class="mdi mdi-plus-circle-outline me-1"></i>
                        Tambah User
                    </a>
                </div>

            </div>
        </div>

        {{-- ================= ALERT ================= --}}
        @if(session('success'))
            <div class="alert alert-success mx-4">
                <i class="mdi mdi-check-circle-outline me-1"></i>
                {{ session('success') }}
            </div>
        @endif

        {{-- ================= BODY CARD ================= --}}
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">

                    {{-- ================= HEADER TABEL ================= --}}
                    <thead style="background-color:#f1f5f9;">
                        <tr class="text-center fw-semibold text-dark">
                            <th width="50">No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>

                    {{-- ================= BODY TABEL ================= --}}
                    <tbody class="text-dark">
                        @foreach($users as $i => $user)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>
                                <strong>{{ $user->name }}</strong>
                            </td>
                            <td>{{ $user->email }}</td>

                            {{-- ================= AKSI ================= --}}
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">

                                    <a href="{{ route('user.edit', $user->id) }}"
                                       class="btn btn-outline-warning btn-sm"
                                       title="Edit User">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>

                                    <form action="{{ route('user.destroy', $user->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-outline-danger btn-sm"
                                                title="Hapus User">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>

    </div>

</div>
@endsection
