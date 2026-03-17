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
                        Manajemen Model Terlatih
                    </h3>
                    <p class="text-muted mb-0">
                        Halaman ini menampilkan daftar model machine learning yang telah dilatih menggunakan algoritma
                        <strong>Random Forest</strong>. Informasi meliputi parameter pelatihan, tingkat akurasi,
                        serta file model yang dapat digunakan kembali untuk proses klasifikasi data anak.
                    </p>
                </div>

                {{-- ===== BUTTON TRAIN ===== --}}
                <div class="col-md-4 text-end mt-3 mt-md-0">
                    <a href="{{ route('klasifikasi.index') }}"
                       class="btn btn-primary shadow-sm px-4">
                        <i class="mdi mdi-plus-circle-outline me-1"></i>
                        Train Model Baru
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
                            <th>Model</th>
                            <th>Algoritma</th>
                            <th width="260">Parameter</th>
                            <th>Akurasi</th>
                            <th>File Model</th>
                            <th>Dibuat</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>

                    {{-- ================= BODY TABEL ================= --}}
                    <tbody class="text-dark">
                    @foreach($models as $i => $m)
                        <tr>

                            <td class="text-center">{{ $i + 1 }}</td>

                            <td>
                                <strong>{{ $m->nama_model }}</strong><br>
                                <small class="text-muted">Status: Aktif</small>
                            </td>

                            <td class="text-center">
                                <span class="badge bg-info text-dark">
                                    {{ $m->algoritma }}
                                </span>
                            </td>

                            <td>
                                <ul class="m-0 ps-3">
                                    <li>n_estimators: {{ $m->parameter['n_estimators'] }}</li>
                                    <li>max_depth: {{ $m->parameter['max_depth'] }}</li>
                                    <li>min_samples_split: {{ $m->parameter['min_samples_split'] }}</li>
                                    <li>test_size: {{ $m->parameter['test_size'] }}</li>
                                </ul>
                            </td>

                            <td class="text-center">
                                <span class="badge bg-success fs-6">
                                    {{ number_format($m->akurasi, 2) }}%
                                </span>
                            </td>

                            <td>
                                @if($m->file_model)
                                    <a href="{{ Storage::url($m->file_model) }}"
                                       target="_blank"
                                       class="text-primary fw-semibold">
                                        {{ basename($m->file_model) }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td class="text-center">
                                {{ $m->created_at->format('d M Y, H:i') }}
                            </td>

                            {{-- ================= AKSI ================= --}}
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">

                                    <a href="{{ route('models.show', $m->id) }}"
                                       class="btn btn-outline-primary btn-sm"
                                       title="Detail Model">
                                        <i class="mdi mdi-eye"></i>
                                    </a>

                                    <form action="{{ route('models.destroy', $m->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus model ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-outline-danger btn-sm"
                                                title="Hapus Model">
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
