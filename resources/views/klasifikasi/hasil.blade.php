@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5">

    <div class="card shadow-sm">

        {{-- ================= HEADER CARD ================= --}}
        <div class="card-body border-bottom pb-3 mb-3">

            <div class="row align-items-center">

                {{-- ===== JUDUL & DESKRIPSI ===== --}}
                <div class="col-md-8">
                    <h3 class="fw-bold text-dark mb-1">
                        Hasil Klasifikasi Status Gizi Balita
                    </h3>
                    <p class="text-muted mb-0">
                        Halaman ini menampilkan riwayat hasil klasifikasi status gizi balita yang telah disimpan.
                        Informasi mencakup <strong>data input balita</strong>, <strong>hasil klasifikasi</strong>,
                        serta <strong>probabilitas setiap kelas</strong> berdasarkan model machine learning yang digunakan.
                    </p>
                </div>

                {{-- ===== BUTTON ===== --}}
                <div class="col-md-4 text-end mt-3 mt-md-0">
                    <a href="{{ route('prediksi.index') }}"
                       class="btn btn-primary shadow-sm px-4">
                        Buat Klasifikasi Baru
                    </a>
                </div>

            </div>

        </div>

        {{-- ================= ALERT ================= --}}
        @if(session('success'))
            <div class="alert alert-success mx-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- ================= BODY CARD ================= --}}
        <div class="card-body pt-0">

            @if($hasil->count() > 0)

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle mb-0">

                    {{-- ================= HEADER TABEL ================= --}}
                    <thead style="background-color:#f1f5f9;">
                        <tr class="text-center fw-semibold text-dark">
                            <th width="50">No</th>
                            <th>Nama Klasifikasi</th>
                            <th>Model</th>
                            <th width="420">Hasil & Data Input</th>
                            <th>Probabilitas (%)</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>

                    {{-- ================= BODY TABEL ================= --}}
                    <tbody class="text-dark">

                    @foreach($hasil as $i => $res)
                        @php
                            $inputs = json_decode($res->input_data, true) ?: [];
                            $kelas  = json_decode($res->kelas, true) ?: [];
                            $probs  = json_decode($res->probabilitas, true) ?: [];
                        @endphp

                        <tr>

                            <td class="text-center">{{ $i + 1 }}</td>

                            <td>
                                <strong>{{ $res->nama_prediksi }}</strong><br>
                                <small class="text-muted">
                                    {{ $res->created_at->format('d M Y, H:i') }}
                                </small>
                            </td>

                            <td class="text-center">
                                <span class="badge bg-info text-dark">
                                    {{ $res->modelML->nama_model ?? $res->model }}
                                </span>
                            </td>

                            {{-- ===== DATA INPUT & HASIL ===== --}}
                            <td class="p-0">

                                <table class="table table-sm table-borderless mb-0">
                                    <thead style="background-color:#f8fafc;">
                                        <tr class="text-center fw-semibold">
                                            <th>JK</th>
                                            <th>Usia</th>
                                            <th>Berat</th>
                                            <th>Tinggi</th>
                                            <th>IMT</th>
                                            <th>Klasifikasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inputs as $idx => $inp)
                                        <tr class="text-center">
                                            <td>{{ $inp['jenis_kelamin'] ?? '-' }}</td>
                                            <td>{{ $inp['usia'] ?? '-' }}</td>
                                            <td>{{ $inp['berat'] ?? '-' }}</td>
                                            <td>{{ $inp['tinggi'] ?? '-' }}</td>
                                            <td>{{ $inp['imt'] ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-success">
                                                    {{ $kelas[$idx] ?? '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </td>

                            {{-- ===== PROBABILITAS ===== --}}
                            <td>
                                @foreach($probs as $p)
                                    @if(is_array($p))
                                        <div class="mb-2">
                                            @foreach($p as $key => $val)
                                                @php
                                                    $color = match($key) {
                                                        'normal' => 'success',
                                                        'wasting' => 'danger',
                                                        'underweight' => 'warning',
                                                        'overweight' => 'info',
                                                        'obesitas' => 'dark',
                                                        default => 'secondary'
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $color }} d-block text-start mb-1">
                                                    {{ ucfirst($key) }} :
                                                    <strong>{{ number_format($val, 1) }}%</strong>
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            </td>

                            {{-- ===== AKSI ===== --}}
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">

                                    <a href="{{ route('hasil.pdf', $res->id) }}"
                                       class="btn btn-outline-success btn-sm">
                                        PDF
                                    </a>

                                    <a href="{{ route('hasil.delete', $res->id) }}"
                                       class="btn btn-outline-danger btn-sm"
                                       onclick="return confirm('Hapus data ini?')">
                                        Hapus
                                    </a>

                                </div>
                            </td>

                        </tr>
                    @endforeach

                    </tbody>

                </table>

            </div>

            @else
                <div class="alert alert-warning mt-3">
                    Tidak ada hasil klasifikasi yang tersimpan.
                </div>
            @endif

        </div>
    </div>

</div>
@endsection
