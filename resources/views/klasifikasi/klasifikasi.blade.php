@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5">

    {{-- ================= CARD UTAMA ================= --}}
    <div class="card shadow-sm">

        {{-- ================= HEADER CARD ================= --}}
        <div class="card-body border-bottom pb-3 mb-3">
            <div class="row align-items-center">
                <h3 class="fw-bold mb-1 text-dark">
                    Random Forest - Klasifikasi Status Gizi Anak
                </h3>
                <p class="text-muted mb-0">
                    Halaman ini digunakan untuk mengimplementasikan algoritma 
                    <strong>Random Forest</strong> dalam mengklasifikasikan status gizi anak
                    berdasarkan data antropometri yang tersedia. Pengguna dapat mengatur
                    parameter model, menentukan pembagian data latih dan data uji, serta
                    menganalisis hasil performa model secara komprehensif.
                </p>
            </div>
        </div>

        {{-- ================= BODY CARD ================= --}}
        <div class="card-body">

            {{-- ================= FORM TRAINING ================= --}}
            <form action="{{ route('klasifikasi.train') }}" method="POST" class="mb-4">
                @csrf

                <div class="card shadow-sm border-0">

                    {{-- ===== HEADER FORM ===== --}}
                    <div class="card-header bg-white fw-bold d-flex align-items-center gap-2">
                        <i class="bi bi-sliders text-primary fs-5"></i>
                        Konfigurasi Parameter Model
                    </div>

                    <div class="card-body">

                        <p class="text-muted mb-4">
                            Atur parameter Random Forest untuk menentukan struktur pohon keputusan
                            dan performa model klasifikasi status gizi anak.
                        </p>

                        <div class="row">

                            <div class="col-md-4 mb-4">
                                <label class="fw-semibold mb-1">Jumlah Pohon (n_estimators)</label>
                                <input type="number" name="n_estimators" class="form-control"
                                    min="10" max="1000"
                                    value="{{ session('n_estimators', 200) }}" required>
                                <small class="text-muted">
                                    Menentukan jumlah pohon keputusan dalam Random Forest.
                                </small>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="fw-semibold mb-1">Max Depth</label>
                                <input type="number" name="max_depth"
                                    class="form-control"
                                    value="{{ session('max_depth') }}">
                                <small class="text-muted">
                                    Kosongkan untuk kedalaman optimal otomatis.
                                </small>
                            </div>

                            <div class="col-md-4 mb-4">
                                <label class="fw-semibold mb-1">Min Samples Split</label>
                                <input type="number" name="min_samples_split"
                                    class="form-control"
                                    min="2" max="20"
                                    value="{{ session('min_samples_split', 2) }}" required>
                                <small class="text-muted">
                                    Jumlah minimum data untuk pemecahan node.
                                </small>
                            </div>

                        </div>

                        <hr class="my-4">

                        <div class="mb-4">
                            <label class="fw-semibold mb-2 d-block">Persentase Data Uji (%) - Tentukan Jumlah Data Uji</label>

                            <div class="d-flex align-items-center gap-3">
                                <input type="range" name="split"
                                    min="10" max="90" step="10"
                                    value="{{ session('split', 20) }}"
                                    class="form-range w-50"
                                    oninput="document.getElementById('splitVal').innerText=this.value">

                                <span id="splitVal" class="badge bg-primary px-3 py-2 fs-6">
                                    {{ session('split', 20) }}
                                </span>
                            </div>

                            <small class="text-muted mt-1 d-block">
                                Tentukan Persentase Data Uji, sisanya data latih. Misal 20% Data Uji , 80 & Data Latih
                            </small>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit"
                                    class="btn btn-primary shadow-sm d-flex align-items-center gap-2 btn-train">
                                <i class="bi bi-cpu fs-5"></i>
                                <span>Latih Model</span>
                            </button>
                        </div>

                    </div>
                </div>
            </form>

            {{-- ================= STYLE BUTTON ================= --}}
            <style>
                .btn-train {
                    background: linear-gradient(135deg, #0d6efd, #3b82f6);
                    border: none;
                    transition: all 0.25s ease-in-out;
                }
                .btn-train:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 6px 16px rgba(13, 110, 253, 0.35);
                }
                .btn-train:active {
                    transform: scale(0.97);
                }
            </style>

            {{-- ================= ERROR API ================= --}}
                @if($errors->has('api_error'))
                    <div class="alert alert-danger">
                        {{ $errors->first('api_error') }}
                    </div>
                @endif

                {{-- ================= HASIL MODEL ================= --}}
                @if(session('results'))
                @php
                    $res    = session('results');
                    $cm     = $res['confusion_matrix'] ?? [];
                    $labels = $res['labels'] ?? [];
                    $report = $res['classification_report'] ?? [];
                @endphp


                <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart-line text-primary"></i>
                    <span class="text-dark">Hasil Training & Evaluasi Model</span>
                </div>
        </div>

        <div class="card-body">

            {{-- ================= TAB NAV ================= --}}
            <ul class="nav nav-pills mb-4 gap-2" role="tablist">
                <li class="nav-item">
                    <button class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-prob">
                        % Probabilitas
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link active"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-metric">
                        Metrik Model
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-cm">
                        Confusion Matrix
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-report">
                        Laporan Klasifikasi
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-tree">
                        Pohon Keputusan
                    </button>
                </li>
            </ul>

            {{-- ================= TAB CONTENT ================= --}}
            <div class="tab-content">

            {{-- ================= TAB PROBABILITAS ================= --}}
            <div class="tab-pane fade" id="tab-prob">

                <h4 class="fw-bold mb-2 text-center text-dark">
                    Probabilitas Hasil Klasifikasi
                </h4>
                <p class="text-muted text-center mb-4">
                    Distribusi probabilitas Klasifikasi Random Forest untuk setiap kelas status gizi anak
                </p>

                <div class="prob-scroll table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        {{-- ===== HEADER TABEL (BIRU MUDA) ===== --}}
                        <thead>
                            <tr class="text-center fw-semibold"
                                style="background-color:#e7f1ff; color:#0d6efd;">
                                <th>JK</th>
                                <th>Usia (bln)</th>
                                <th>Berat</th>
                                <th>Tinggi</th>
                                <th>IMT</th>
                                <th>Status Aktual</th>
                                <th>Klasifikasi</th>
                                @foreach($labels as $label)
                                    <th width="240px">% {{ ucfirst($label) }}</th>
                                @endforeach
                            </tr>
                        </thead>

                        {{-- ===== BODY TABEL ===== --}}
                        <tbody>
                            @foreach($res['batch_results'] ?? [] as $row)
                            <tr class="text-center text-dark">

                                <td>{{ $row['jenis_kelamin'] }}</td>
                                <td>{{ $row['usia_bulan'] }}</td>
                                <td>{{ $row['berat_badan'] }}</td>
                                <td>{{ $row['tinggi_badan'] }}</td>
                                <td>{{ $row['imt'] }}</td>

                                <td>
                                    <span class="badge bg-warning bg-opacity-25 text-dark">
                                        {{ $row['status_gizi_aktual'] }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-primary bg-opacity-25 text-dark fw-semibold">
                                        {{ $row['prediksi'] }}
                                    </span>
                                </td>

                                @foreach($labels as $label)
                                    @php $p = $row['probabilitas'][$label] ?? 0; @endphp
                                    <td>
                                        <div class="d-flex align-items-center gap-2">

                                            <div class="progress flex-grow-1"
                                                style="height:8px; background-color:#e9ecef;">
                                                <div class="progress-bar
                                                    @if($p >= 70) bg-success
                                                    @elseif($p >= 40) bg-primary
                                                    @elseif($p >= 20) bg-warning
                                                    @else bg-danger
                                                    @endif"
                                                    role="progressbar"
                                                    style="width: {{ $p }}%;">
                                                </div>
                                            </div>

                                            <span class="fw-semibold text-dark"
                                                style="width:50px;">
                                                {{ number_format($p, 1) }}%
                                            </span>

                                        </div>
                                    </td>
                                @endforeach

                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>

            {{-- ================= TAB METRIK MODEL ================= --}}
            <div class="tab-pane fade show active" id="tab-metric">

                <h4 class="fw-bold mb-2 text-center text-dark">
                    Evaluasi Kinerja Model
                </h4>
                <p class="text-muted text-center mb-4">
                    Ringkasan performa model Random Forest berdasarkan hasil training dan pengujian
                </p>

                <div class="d-flex flex-wrap gap-4 mb-4">

                    {{-- ===== AKURASI ===== --}}
                    <div class="card flex-fill shadow-sm border-0 metric-card metric-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1">Akurasi Model</h6>
                                    <h3 class="fw-bold text-dark mb-0">
                                        {{ round($res['accuracy'] * 100, 2) }}%
                                    </h3>
                                </div>
                                <div class="metric-icon bg-success bg-opacity-10 text-success">
                                    <i class="mdi mdi-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== WAKTU TRAINING ===== --}}
                    <div class="card flex-fill shadow-sm border-0 metric-card metric-primary">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1">Waktu Training</h6>
                                    <h4 class="fw-bold text-dark mb-0">
                                        {{ isset($res['training_time']) ? round($res['training_time'], 2) . ' detik' : 'N/A' }}
                                    </h4>
                                </div>
                                <div class="metric-icon bg-primary bg-opacity-10 text-primary">
                                    <i class="mdi mdi-clock-outline"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== JUMLAH DATA LATIH ===== --}}
                    <div class="card flex-fill shadow-sm border-0 metric-card metric-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="text-muted mb-1">Training Samples</h6>
                                    <h4 class="fw-bold text-dark mb-0">
                                        {{ count($res['train_data'] ?? []) }}
                                    </h4>
                                </div>
                                <div class="metric-icon bg-warning bg-opacity-10 text-warning">
                                    <i class="mdi mdi-database"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <style>
                    .metric-card {
                        border-radius: 14px;
                        transition: all 0.25s ease-in-out;
                        cursor: pointer;
                    }

                    .metric-card:hover {
                        transform: translateY(-3px);
                        box-shadow: 0 12px 28px rgba(0,0,0,0.12);
                    }

                    .metric-icon {
                        width: 50px;
                        height: 50px;
                        border-radius: 14px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 1.6rem;
                    }

                    .metric-success { border-left: 4px solid #198754; }
                    .metric-primary { border-left: 4px solid #0d6efd; }
                    .metric-warning { border-left: 4px solid #ffc107; }
                </style>

                <div class="d-flex flex-wrap gap-4">
                    <div class="flex-fill">
                        <h6 class="fw-bold mb-2 text-dark">Informasi Training</h6>
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <th>Total Data</th>
                                    <td>{{ $res['preprocessing']['total_data'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Data Latih</th>
                                    <td>{{ count($res['train_data'] ?? []) }}</td>
                                </tr>
                                <tr>
                                    <th>Data Uji</th>
                                    <td>{{ count($res['test_data'] ?? []) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex-fill">
                        <h6 class="fw-bold mb-2 text-dark">Parameter Model</h6>
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <th>Jumlah Pohon</th>
                                    <td>{{ session('n_estimators') }}</td>
                                </tr>
                                <tr>
                                    <th>Max Depth</th>
                                    <td>{{ session('max_depth') ?? 'default' }}</td>
                                </tr>
                                <tr>
                                    <th>Min Samples Split</th>
                                    <td>{{ session('min_samples_split') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ================= TAB CONFUSION MATRIX ================= --}}
            <div class="tab-pane fade" id="tab-cm">

                <h4 class="fw-bold mb-2 text-center text-dark">
                    Confusion Matrix
                </h4>
                <p class="text-muted text-center mb-4">
                    Perbandingan antara label aktual dan hasil Klasifikasi model
                </p>

                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2">Aktual \ Klasifikasi</th>
                                <th colspan="{{ count($labels) }}">Klasifikasi</th>
                            </tr>
                            <tr>
                                @foreach($labels as $label)
                                    <th>{{ $label }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cm as $i => $row)
                                <tr>
                                    <th class="bg-light">{{ $labels[$i] }}</th>
                                    @foreach($row as $j => $val)
                                        <td class="{{ $i === $j ? 'bg-success bg-opacity-25' : 'bg-danger bg-opacity-10' }}">
                                            <strong>{{ $val }}</strong>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 text-center">
                    <span class="badge bg-success bg-opacity-75">Klasifikasi Benar</span>
                    <span class="badge bg-danger bg-opacity-75">Klasifikasi Salah</span>
                </div>
            </div>

            {{-- ================= TAB LAPORAN ================= --}}
            <div class="tab-pane fade" id="tab-report">

                <h4 class="fw-bold mb-2 text-center text-dark">
                    Laporan Klasifikasi
                </h4>
                <p class="text-muted text-center mb-4">
                    Ringkasan performa model untuk setiap kelas status gizi
                </p>

                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>KELAS</th>
                                <th>PRECISION</th>
                                <th>RECALL</th>
                                <th>F1-SCORE</th>
                                <th>SUPPORT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report as $row)
                                @php
                                    $isSummary = in_array(strtolower($row['kelas'] ?? ''), ['macro avg','weighted avg','accuracy']);
                                @endphp
                                <tr class="{{ $isSummary ? 'bg-info bg-opacity-25 fw-bold' : '' }}">
                                    <td>{{ $row['kelas'] ?? '-' }}</td>
                                    <td>{{ number_format($row['precision'] ?? 0, 3) }}</td>
                                    <td>{{ number_format($row['recall'] ?? 0, 3) }}</td>
                                    <td>{{ number_format($row['f1'] ?? 0, 3) }}</td>
                                    <td>{{ $row['support'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 text-center">
                    <span class="badge bg-success bg-opacity-75">Excellent (≥ 0.8)</span>
                    <span class="badge bg-warning bg-opacity-75">Good (≥ 0.6)</span>
                    <span class="badge bg-danger bg-opacity-75">Needs Improvement (&lt; 0.6)</span>
                </div>
            </div>

            {{-- ================= TAB ANALISIS POHON ================= --}}
                <div class="tab-pane fade" id="tab-tree">

                    <h4 class="fw-bold text-center mb-2">
                        Pohon Keputusan (Interpretasi Model)
                    </h4>
                    <p class="text-muted text-center mb-4">
                        Fitur ini digunakan untuk menjelaskan proses pengambilan keputusan
                        pada setiap pohon keputusan dalam algoritma Random Forest.
                        Pengguna dapat memilih indeks pohon tertentu untuk melihat struktur,
                        aturan keputusan (IF–THEN), serta visualisasi pohon secara lengkap.
                    </p>

                    @php
                        $tree = $res['tree_analysis'] ?? [];
                    @endphp

                    <form method="POST" action="{{ route('klasifikasi.tree_analysis') }}" class="mb-4">
                    @csrf

                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="fw-semibold mb-1">Pilih Pohon Keputusan</label>
                            <select name="tree_index" class="form-select">
                                @for($i = 0; $i < session('n_estimators', 1); $i++)
                                    <option value="{{ $i }}"
                                        {{ ($tree['tree_info']['tree_index'] ?? 0) == $i ? 'selected' : '' }}>
                                        Pohon ke-{{ $i + 1 }}
                                    </option>
                                @endfor
                            </select>
                            <small class="text-muted">
                                Pilih salah satu pohon keputusan dalam Random Forest
                            </small>
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-primary w-100">
                                <i class="bi bi-diagram-3"></i> Tampilkan
                            </button>
                        </div>
                    </div>
                </form>

                {{-- ================= HASIL ANALISIS POHON ================= --}}
                @if(isset($tree['tree_info']))

                {{-- ================= INFO POHON ================= --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light fw-semibold">
                        Informasi Pohon Keputusan
                    </div>
                    <table class="table table-bordered table-sm w-50">
                        <tr>
                            <th>Indeks Pohon</th>
                            <td>{{ $tree['tree_info']['tree_index'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Kedalaman Pohon</th>
                            <td>{{ $tree['tree_info']['depth'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah Node</th>
                            <td>{{ $tree['tree_info']['node_count'] ?? '-' }}</td>
                        </tr>
                    </table>

                    <small class="text-muted">
                        Pohon yang ditampilkan merupakan <strong>pohon ke-{{ ($tree['tree_info']['tree_index'] ?? 0) + 1 }}</strong>
                        dari keseluruhan Random Forest.
                    </small>

                </div>

                {{-- ================= VISUALISASI POHON ================= --}}
                @if(!empty($tree['image_base64']))
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light fw-semibold">
                        Visualisasi Pohon Keputusan
                    </div>
                    <div class="card-body text-center">

                        <img src="data:image/png;base64,{{ $tree['image_base64'] }}"
                            class="img-fluid border rounded"
                            alt="Decision Tree Visualization">

                        <small class="text-muted d-block mt-2">
                            Visualisasi lengkap pohon keputusan ke-{{ ($tree['tree_info']['tree_index'] ?? 0) + 1 }}
                        </small>

                    </div>
                </div>
                @endif

                {{-- ================= FEATURE IMPORTANCE ================= --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light fw-semibold">
                        Feature Importance (Kontribusi Fitur)
                    </div>
                    <div class="card-body">

                        <p class="text-muted mb-3">
                            Feature importance menunjukkan tingkat pengaruh setiap variabel
                            dalam proses klasifikasi status gizi anak.
                        </p>

                        <table class="table table-bordered align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Fitur</th>
                                    <th>Nilai Importance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tree['feature_importance'] ?? [] as $f)
                                    <tr>
                                        <td class="fw-semibold">{{ $f['fitur'] }}</td>
                                        <td>{{ $f['importance'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted">
                        Nilai feature importance dihitung berdasarkan keseluruhan Random Forest,
                        bukan hanya satu pohon keputusan.
                    </small>
                </div>

                {{-- ================= ATURAN IF–THEN ================= --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light fw-semibold">
                        Aturan Keputusan (IF–THEN Rules)
                    </div>
                    <div class="card-body">

                        <p class="text-muted mb-3">
                            Aturan berikut merupakan kumpulan aturan keputusan (IF–THEN)
                            yang dihasilkan dari seluruh leaf node pada pohon keputusan terpilih.
                            Setiap aturan merepresentasikan jalur keputusan dari root hingga leaf.
                        </p>

                        <div class="accordion" id="ruleAccordion">
                            @foreach($tree['rules'] ?? [] as $i => $rule)
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#rule{{ $i }}">
                                            Aturan Keputusan #{{ $i + 1 }}
                                        </button>
                                    </h2>
                                    <div id="rule{{ $i }}"
                                        class="accordion-collapse collapse"
                                        data-bs-parent="#ruleAccordion">
                                        <div class="accordion-body">

                                            <p class="mb-2">
                                                <strong>Jika:</strong><br>
                                                {{ $rule['aturan'] }}
                                            </p>

                                            <p class="mb-2">
                                                <strong>Maka status gizi diprediksi sebagai:</strong>
                                                <span class="badge bg-primary">
                                                    {{ $rule['prediksi'] }}
                                                </span>
                                            </p>

                                            <small class="text-muted">
                                                Gini: {{ $rule['gini'] }},
                                                Jumlah data: {{ $rule['samples'] }}
                                            </small>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                    @else
                    {{-- ================= STATE AWAL ================= --}}
                    <div class="alert alert-info text-center">
                        <strong>Silakan pilih pohon keputusan</strong><br>
                        Pilih indeks pohon dan klik tombol <b>Tampilkan</b>
                        untuk menampilkan hasil analisis pohon keputusan.
                    </div>
                    @endif
                </div>
            </div>

            </div>
            {{-- ================= END TAB CONTENT ================= --}}


            {{-- =================== FORM SIMPAN & HAPUS MODEL =================== --}}
            <div class="d-flex flex-wrap align-items-end justify-content-end gap-3 mt-4 border-top pt-3">

                <form action="{{ route('klasifikasi.save_model') }}" method="POST" class="d-flex align-items-end gap-2">
                    @csrf
                    <div class="flex-grow-1" style="min-width: 250px;">
                        <label class="fw-semibold">Nama Model</label>
                        <input type="text"
                            name="nama_model"
                            class="form-control"
                            value="Model_RF_{{ date('Ymd_His') }}"
                            required>
                    </div>

                    <input type="hidden" name="algoritma" value="Random Forest">
                    <input type="hidden" name="akurasi" value="{{ $res['accuracy'] * 100 }}">
                    <input type="hidden" name="parameter" value="{{ json_encode([
                        'n_estimators' => session('n_estimators'),
                        'max_depth' => session('max_depth'),
                        'min_samples_split' => session('min_samples_split'),
                        'test_size' => session('split')
                    ]) }}">
                    <input type="hidden" name="file_model" value="{{ $res['model_path'] ?? null }}">

                    <button type="submit"
                            class="btn btn-primary btn-lg shadow-sm d-flex align-items-center gap-2 btn-train">
                        <i class="mdi mdi-content-save"></i>
                        <span>Simpan Model</span>
                    </button>
                    
                </form>

                <form action="{{ route('klasifikasi.clear_results') }}" method="POST">
                    @csrf
                    <button class="btn btn-danger btn-lg shadow-sm"
                            onclick="return confirm('Hapus hasil training saat ini?')">
                        <i class="bi bi-trash me-1"></i> Hapus Hasil Training
                    </button>
                </form>

            </div>
        </div>

        @endif

    </div>
</div>

{{-- ================= CARD VISUALISASI HASIL ================= --}}
@if(session('results'))
<div class="card shadow-sm border-0 mt-4">

    <div class="card-header bg-white fw-bold d-flex align-items-center gap-2">
        <i class="bi bi-pie-chart-fill text-primary"></i>
        <span>Visualisasi Hasil Klasifikasi</span>
    </div>

    <div class="card-body">

        <p class="text-muted mb-4">
            Visualisasi distribusi status gizi aktual dan hasil prediksi model
            Random Forest berdasarkan data uji.
        </p>

        <div class="row g-4">

            {{-- PIE CHART --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light fw-semibold">
                        Distribusi Status Gizi Aktual
                    </div>
                    <div class="card-body">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- BAR CHART --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light fw-semibold">
                        Distribusi Hasil Prediksi Model
                    </div>
                    <div class="card-body">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endif


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>


<script>

    Chart.register(ChartDataLabels);

    document.addEventListener('DOMContentLoaded', function () {

        const batchResults = @json($res['batch_results'] ?? []);

        if (batchResults.length === 0) {
            console.warn('Tidak ada data untuk divisualisasikan');
            return;
        }

        // ==============================
        // AGREGASI DATA
        // ==============================
        const actualCount = {};
        const predictCount = {};

        batchResults.forEach(item => {
            actualCount[item.status_gizi_aktual] =
                (actualCount[item.status_gizi_aktual] || 0) + 1;

            predictCount[item.prediksi] =
                (predictCount[item.prediksi] || 0) + 1;
        });

        // ==============================
        // PIE CHART
        // ==============================
        new Chart(document.getElementById('pieChart'), {
            type: 'pie',
            data: {
                labels: Object.keys(actualCount),
                datasets: [{
                    data: Object.values(actualCount),
                    backgroundColor: [
                        '#0d6efd',
                        '#198754',
                        '#ffc107',
                        '#dc3545',
                        '#6f42c1'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    datalabels: {
                        display: true,
                        color: '#ffffff',
                        font: {
                            weight: 'bold',
                            size: 14
                        },
                        formatter: (value, ctx) => {
                            const data = ctx.chart.data.datasets[0].data;
                            const total = data.reduce((a, b) => a + b, 0);
                            const percent = ((value / total) * 100).toFixed(1);
                            return `${value}\n${percent}%`;
                        }
                    }
                }
            }
        });



        // ==============================
        // BAR CHART
        // ==============================
        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(predictCount),
                datasets: [{
                    data: Object.values(predictCount),
                    backgroundColor: '#0d6efd'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    datalabels: {
                        display: true,
                        anchor: 'end',
                        align: 'top',
                        offset: 4,
                        color: '#000',
                        font: {
                            weight: 'bold',
                            size: 13
                        },
                        formatter: value => value
                    }
                }
            }
        });



    });
</script>


@endsection
