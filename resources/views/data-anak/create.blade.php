@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-10 mx-auto grid-margin stretch-card">
        <div class="card shadow-sm border-0">
            <div class="card-body">

                {{-- HEADER --}}
                <h3 class="card-title fw-bold mb-1">Tambah Data Anak</h3>

                {{-- PARAGRAF PEMBUKA DI SINI --}}
                <p class="text-muted fs-6 mb-4">
                    Halaman ini digunakan untuk menambahkan data anak ke dalam dataset 
                    klasifikasi status gizi. Pastikan data yang diinput sudah benar, 
                    karena akan digunakan untuk proses pelatihan model machine learning.
                </p>

                {{-- FORM --}}
                <form action="{{ route('data-anak.store') }}" method="POST">
                    @csrf

                    {{-- JENIS KELAMIN --}}
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control form-control-lg" required>
                            <option value="">-- Pilih --</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    {{-- USIA --}}
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Usia (Bulan)</label>
                        <input type="number" name="usia_bulan" class="form-control form-control-lg" required>
                    </div>

                    {{-- BERAT BADAN --}}
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Berat Badan (kg)</label>
                        <input type="number" step="0.01" name="berat_badan" id="berat_badan"
                               class="form-control form-control-lg" required>
                    </div>

                    {{-- TINGGI BADAN --}}
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Tinggi Badan (cm)</label>
                        <input type="number" step="0.01" name="tinggi_badan" id="tinggi_badan"
                               class="form-control form-control-lg" required>
                    </div>

                    {{-- IMT --}}
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Indeks Massa Tubuh (IMT)</label>
                        <input type="number" step="0.01" name="imt" id="imt"
                               class="form-control form-control-lg bg-light" readonly>
                    </div>

                    {{-- STATUS GIZI --}}
                    <div class="form-group mb-4">
                        <label class="fw-semibold">Status Gizi</label>
                        <select name="status_gizi" class="form-control form-control-lg" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="obesitas">Obesitas</option>
                            <option value="stunting">Stunting</option>
                            <option value="wasting">Wasting</option>
                            <option value="underweight">Underweight</option>
                        </select>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('data-anak.index') }}" class="btn btn-light btn-lg px-4">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>

                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            <i class="mdi mdi-content-save"></i> Simpan Data
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function hitungIMT() {
        let bb = parseFloat(document.getElementById('berat_badan').value);
        let tb = parseFloat(document.getElementById('tinggi_badan').value);

        if (bb > 0 && tb > 0) {
            let tbMeter = tb / 100;
            let imt = bb / (tbMeter * tbMeter);
            document.getElementById('imt').value = imt.toFixed(2);
        }
    }

    document.getElementById('berat_badan').addEventListener('input', hitungIMT);
    document.getElementById('tinggi_badan').addEventListener('input', hitungIMT);
</script>
@endpush
