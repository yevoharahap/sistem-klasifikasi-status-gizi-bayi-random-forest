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
                        Klasifikasi Status Gizi Balita
                    </h3>
                    <p class="text-muted mb-0">
                        Halaman ini digunakan untuk melakukan Klasifikasi status gizi balita berdasarkan
                        <strong>usia</strong>, <strong>berat badan</strong>, <strong>tinggi badan</strong>,
                        dan <strong>IMT</strong> menggunakan model machine learning yang telah dilatih.
                        Anda dapat memasukkan lebih dari satu data sekaligus dalam satu proses prediksi.
                    </p>
                </div>

            </div>
        </div>

        {{-- ================= BODY CARD ================= --}}
        <div class="card-body pt-0">

            {{-- ================= FORM PREDIKSI ================= --}}
            <form action="{{ route('prediksi.run') }}" method="POST">
                @csrf

                {{-- ===== NAMA PREDIKSI ===== --}}
                <div class="mb-3">
                    <label class="fw-semibold">Nama Klasifikasi</label>
                    <input type="text"
                           name="nama_prediksi"
                           class="form-control"
                           value="{{ old('nama_prediksi') }}"
                           placeholder="Contoh: Klasifikasi Bayi 1"
                           required>
                    <small class="text-muted">
                        Digunakan sebagai identitas untuk menyimpan dan meninjau kembali hasil klasifikasi.
                    </small>
                </div>

                {{-- ===== PILIH MODEL ===== --}}
                <div class="mb-3">
                    <label class="fw-semibold">Pilih Model Machine Learning</label>

                    @if($models->count() > 0)
                        <select name="model_file" class="form-control" required>
                            @foreach($models as $model)
                                <option value="{{ $model->file_model }}"
                                    {{ ($selected_model_file == $model->file_model) ? 'selected' : '' }}>
                                    {{ $model->nama_model }} ({{ $model->algoritma }}) —
                                    Akurasi {{ number_format($model->akurasi, 2) }}%
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            Model dengan akurasi lebih tinggi cenderung menghasilkan klasifikasi yang lebih baik.
                        </small>
                    @else
                        <p class="text-danger mb-0">Belum tersedia model yang dapat digunakan.</p>
                    @endif
                </div>

                <hr>

                {{-- ================= DATA INPUT ================= --}}
                <h5 class="fw-bold mb-1">Data Balita</h5>
                <p class="text-muted mb-3">
                    Masukkan data balita. Nilai IMT akan dihitung otomatis berdasarkan berat dan tinggi badan.
                </p>

       <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Jenis Kelamin</th>
                    <th>Usia (bulan)</th>
                    <th>Berat (kg)</th>
                    <th>Tinggi (cm)</th>
                    <th>IMT</th>
                    <th width="60">Aksi</th>
                </tr>
            </thead>
            <tbody id="input-rows">

                @php
                    $oldUsia = old('usia', []);
                    $rowCount = count($oldUsia) > 0 ? count($oldUsia) : 3;
                @endphp

                @for($i = 0; $i < $rowCount; $i++)
                    <tr>
                        <td class="row-number">{{ $i + 1 }}</td>

                        <td>
                            <select name="jenis_kelamin[]" class="form-control">
                                <option value="L" {{ old('jenis_kelamin.'.$i) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('jenis_kelamin.'.$i) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </td>

                        <td>
                            <input type="number" step="0.01" name="usia[]" class="form-control usia"
                                value="{{ old('usia.'.$i) }}">
                        </td>

                        <td>
                            <input type="number" step="0.01" name="berat[]" class="form-control berat"
                                value="{{ old('berat.'.$i) }}">
                        </td>

                        <td>
                            <input type="number" step="0.01" name="tinggi[]" class="form-control tinggi"
                                value="{{ old('tinggi.'.$i) }}">
                        </td>

                        <td>
                            <input type="number" step="0.01" name="imt[]" class="form-control imt"
                                value="{{ old('imt.'.$i) }}" readonly>
                        </td>

                        <td>
                            <button type="button" class="btn btn-danger btn-sm delete-row">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </td>
                    </tr>
                @endfor

            </tbody>
        </table>

                {{-- ===== BUTTON ===== --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" id="addRow" class="btn btn-success btn-sm">
                        Tambah Baris
                    </button>

                    <button type="submit" class="btn btn-primary px-4">
                        Lakukan Klasifikasi
                    </button>
                </div>

            </form>

        {{-- =================== MODEL INFO =================== --}}
            @if(session('model_info'))
                <div class="row mt-4">

                    <div class="col-md-4">
                        <div class="card shadow-sm p-3" style="border-radius: 15px;">
                            <div><span class="badge bg-success">Berhasil</span></div>
                            <h2 class="fw-bold mt-2">{{ number_format(session('model_info')['akurasi'], 1) }}%</h2>
                            <p class="text-muted">Akurasi Model<br><small class="text-success">Training Accuracy</small></p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm p-3" style="border-radius: 15px;">
                            <div><span class="badge bg-primary">Batch</span></div>
                            <h5 class="fw-bold mt-2">{{ session('model_info')['nama_model'] }} - {{ session('model_info')['algoritma'] }}</h5>
                            <p class="text-muted">Model yang Digunakan<br>
                                <small class="text-primary">{{ session('model_info')['algoritma'] }}</small>
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card shadow-sm p-3" style="border-radius: 15px;">
                            <div><span class="badge bg-warning text-dark">Performance</span></div>
                            <h4 class="fw-bold mt-2">{{ session('model_info')['execution_time'] }}s</h4>
                            <p class="text-muted">Waktu Eksekusi<br><small class="text-warning">status</small></p>
                        </div>
                    </div>

                </div>
            @endif



            {{-- ======================== HASIL PREDIKSI ======================== --}}
        @if(session('result'))
        <div class="mt-5">

            <ul class="nav nav-tabs" id="prediksiTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="prob-tab" data-bs-toggle="tab" href="#prob" role="tab">
                        % Probabilitas
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="result-tab" data-bs-toggle="tab" href="#result" role="tab">
                        ✔ Result
                    </a>
                </li>
            </ul>

            <div class="tab-content border p-4 bg-white shadow-sm" style="border-radius: 0 0 10px 10px;">

                {{-- PROBABILITAS --}}
                <div class="tab-pane fade show active" id="prob" role="tabpanel">

                    <h5 class="fw-bold mb-3">Probabilitas Prediksi per Data (%)</h5>

                    @php
                        $sample = session('result')[0]['probabilitas'] ?? [];
                        $labels = array_keys($sample);
                    @endphp

                    <div class="table-responsive prob-scroll">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>Data</th>
                                    @foreach($labels as $lbl)
                                        <th width="240px">% {{ ucfirst($lbl) }}</th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody>
                                @foreach(session('result') as $i => $item)
                                <tr class="text-center">
                                    <td><span class="badge bg-primary">Data {{ $i+1 }}</span></td>

                                    @foreach($labels as $lbl)
                                        @php $p = $item['probabilitas'][$lbl] ?? 0; @endphp

                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 12px;">
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

                                                <span class="fw-bold" style="width: 50px;">
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

                {{-- RESULT --}}
                <div class="tab-pane fade" id="result" role="tabpanel">

                    <h4 class="fw-bold">Hasil Klasifikasi</h4>

                    <table class="table table-hover mt-3">
                        <thead class="table-success">
                            <tr>
                                <th>No</th>
                                <th>Jenis Kelamin</th>
                                <th>Usia</th>
                                <th>Berat</th>
                                <th>Tinggi</th>
                                <th>IMT</th>
                                <th>Klasifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(session('result') as $res)
                            <tr>
                                <td>{{ $res['no'] }}</td>
                                <td>{{ $res['input']['jenis_kelamin'] }}</td>
                                <td>{{ $res['input']['usia'] }}</td>
                                <td>{{ $res['input']['berat'] }}</td>
                                <td>{{ $res['input']['tinggi'] }}</td>
                                <td>{{ $res['input']['imt'] }}</td>
                                <td>
                                    <span class="badge bg-success px-3 py-2">
                                        ✔ {{ $res['hasil'] }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

            </div>

            {{-- ================= BUTTON SIMPAN & HAPUS DI SUDUT KANAN ================= --}}
            <div class="mt-3 d-flex justify-content-end gap-3">

                {{-- Tombol Hapus --}}
                <form action="{{ route('prediksi.clear') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        🗑 Hapus Hasil Klasifikasi
                    </button>
                </form>

                {{-- Tombol Simpan --}}
                <form action="{{ route('prediksi.save') }}" method="POST">
                    @csrf
                    <input type="hidden" name="nama_prediksi" value="{{ session('nama_prediksi') }}">
                    <input type="hidden" name="model" value="{{ session('model_file') }}">
                    <input type="hidden" name="data" value="{{ json_encode(session('result')) }}">

                    <button type="submit" class="btn btn-success">
                        Simpan Hasil Klasifikasi
                    </button>
                </form>

            </div>
            {{-- ======================================================================= --}}

        </div>
        @endif
    </div>
</div> 
       
        @push('scripts')
        <script>
        function calculateIMT(row) {
            let berat = parseFloat(row.querySelector('.berat').value);
            let tinggi = parseFloat(row.querySelector('.tinggi').value);

            if (berat > 0 && tinggi > 0) {
                let imt = berat / Math.pow((tinggi / 100), 2);
                row.querySelector('.imt').value = imt.toFixed(2);
            }
        }

        document.getElementById('addRow').addEventListener('click', function () {
            let tbody = document.getElementById('input-rows');
            let newRow = tbody.rows[0].cloneNode(true);

            newRow.querySelectorAll('input').forEach(input => input.value = "");
            newRow.querySelectorAll('select').forEach(sel => sel.selectedIndex = 0);

            newRow.querySelector('.row-number').textContent = tbody.rows.length + 1;

            tbody.appendChild(newRow);
            updateRowCount();
        });

        document.getElementById('input-rows').addEventListener('click', function (e) {
            if (e.target.closest('.delete-row')) {
                let rows = document.querySelectorAll('#input-rows tr');

                if (rows.length > 1) {
                    e.target.closest('tr').remove();
                    resetRowNumbers();
                    updateRowCount();
                }
            }
        });

        function resetRowNumbers() {
            document.querySelectorAll('#input-rows tr').forEach((tr, i) => {
                tr.querySelector('.row-number').textContent = i + 1;
            });
        }

        function updateRowCount() {
            document.getElementById('rowCount').textContent =
                document.querySelectorAll('#input-rows tr').length;
        }

        document.addEventListener('input', function (e) {
            if (e.target.classList.contains('berat') || e.target.classList.contains('tinggi')) {
                calculateIMT(e.target.closest('tr'));
            }
        });
        </script>
        @endpush

        @endsection
