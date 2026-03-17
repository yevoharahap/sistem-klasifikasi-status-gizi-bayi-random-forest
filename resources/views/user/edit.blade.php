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
                        Edit User
                    </h3>
                    <p class="text-muted mb-0">
                        Gunakan form ini untuk mengubah data user. Jika tidak ingin mengubah password, biarkan kolom
                        password kosong.
                    </p>
                </div>
            </div>
        </div>

        {{-- ================= ALERT ERROR ================= --}}
        @if ($errors->any())
            <div class="alert alert-danger mx-4">
                <i class="mdi mdi-alert-circle-outline me-1"></i>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ================= BODY CARD ================= --}}
        <div class="card-body pt-0">
            <form action="{{ route('user.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password <small class="text-muted">(kosongkan jika tidak diubah)</small></label>
                    <input type="password" name="password" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success shadow-sm">
                        <i class="mdi mdi-content-save-outline me-1"></i>
                        Update
                    </button>
                    <a href="{{ route('user.index') }}" class="btn btn-secondary shadow-sm">
                        <i class="mdi mdi-arrow-left me-1"></i>
                        Batal
                    </a>
                </div>

            </form>
        </div>

    </div>

</div>
@endsection
