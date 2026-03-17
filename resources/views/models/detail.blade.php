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
                        Detail Model Terlatih
                    </h3>
                    <p class="text-muted mb-0">
                        Halaman ini menampilkan informasi lengkap mengenai model machine learning
                        yang telah dilatih, meliputi algoritma yang digunakan, parameter pelatihan,
                        tingkat akurasi, serta file model yang dapat digunakan kembali.
                    </p>
                </div>

                {{-- ===== BUTTON KEMBALI ===== --}}
                <div class="col-md-4 text-end mt-3 mt-md-0">
                    <a href="{{ route('models.index') }}"
                       class="btn btn-secondary shadow-sm px-4">
                        <i class="mdi mdi-arrow-left me-1"></i>
                        Kembali
                    </a>
                </div>

            </div>

        </div>

        {{-- ================= BODY CARD ================= --}}
        <div class="card-body pt-0">

            <div class="table-responsive">

                <table class="table table-bordered align-middle mb-0">

                    <tbody class="text-dark">

                        <tr>
                            <th width="30%" style="background-color:#f1f5f9;">
                                Nama Model
                            </th>
                            <td>
                                <strong>{{ $model->nama_model }}</strong>
                            </td>
                        </tr>

                        <tr>
                            <th style="background-color:#f1f5f9;">
                                Algoritma
                            </th>
                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ $model->algoritma }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <th style="background-color:#f1f5f9;">
                                Akurasi
                            </th>
                            <td>
                                <span class="badge bg-success fs-6">
                                    {{ number_format($model->akurasi, 2) }}%
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <th style="background-color:#f1f5f9;">
                                Parameter Model
                            </th>
                            <td>
                                <ul class="m-0 ps-3">
                                    <li>n_estimators: {{ $model->parameter['n_estimators'] }}</li>
                                    <li>max_depth: {{ $model->parameter['max_depth'] }}</li>
                                    <li>min_samples_split: {{ $model->parameter['min_samples_split'] }}</li>
                                    <li>test_size: {{ $model->parameter['test_size'] }}</li>
                                </ul>
                            </td>
                        </tr>

                        <tr>
                            <th style="background-color:#f1f5f9;">
                                File Model
                            </th>
                            <td>
                                @if($model->file_model)
                                    <a href="{{ Storage::url($model->file_model) }}"
                                       target="_blank"
                                       class="text-primary fw-semibold">
                                        {{ basename($model->file_model) }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th style="background-color:#f1f5f9;">
                                Tanggal Dibuat
                            </th>
                            <td>
                                {{ $model->created_at->format('d M Y, H:i') }}
                            </td>
                        </tr>

                    </tbody>
                </table>

            </div>

        </div>
    </div>

</div>
@endsection
