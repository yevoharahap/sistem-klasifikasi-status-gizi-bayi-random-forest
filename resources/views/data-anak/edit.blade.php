@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-10 mx-auto grid-margin stretch-card">
        <div class="card shadow-sm border-0">
            <div class="card-body">

                {{-- HEADER --}}
                <h3 class="card-title fw-bold mb-1">Edit Data Anak</h3>

                {{-- PARAGRAF --}}
                <p class="text-muted fs-6 mb-4">
                    Halaman ini digunakan untuk memperbarui data anak yang sudah tersimpan 
                    dalam dataset klasifikasi status gizi. Pastikan perubahan yang Anda lakukan 
                    sudah benar agar tidak memengaruhi kualitas pelatihan model machine learning.
                </p>

                {{-- FORM --}}
                <form action="{{ route('data-anak.update', $anak->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- JENIS KELAMIN --}}
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control form-control-lg" required>
                            <option value="L" {{ $anak->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ $anak->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    {{-- USIA --}}
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Usia (Bulan)</label>
                        <input type="number" name="usia_bulan"
                               value="{{ $anak->usia_bulan }}"
                               class="form-control form-control-lg" required>
                    </div>

                    {{-- BERAT BADAN --}}
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Berat Badan (kg)</label>
                        <input type="number" step="0.01" name="berat_badan"
                               id="berat_badan" value="{{ $anak->berat_badan }}"
                               class="form-control form-control-lg" required>
                    </div>

                    {{-- TINGGI BADAN --}}
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Tinggi Badan (cm)</label>
                        <input type="number" step="0.01" name="tinggi_badan"
                               id="tinggi_badan" value="{{ $anak->tinggi_badan }}"
                               class="form-control form-control-lg" required>
                    </div>

                    {{-- IMT --}}
                    <div class="form-group mb-3">
                        <label class="fw-semibold">Indeks Massa Tubuh (IMT)</label>
                        <input type="number" step="0.01" name="imt"
                               id="imt" value="{{ number_format($anak->imt, 2) }}"
                               class="form-control form-control-lg bg-light" readonly>
                    </div>

                    {{-- STATUS GIZI --}}
                    <div class="form-group mb-4">
                        <label class="fw-semibold">Status Gizi</label>
                        <select name="status_gizi" class="form-control form-control-lg" required>
                            <option value="obesitas" {{ $anak->status_gizi == 'obesitas' ? 'selected' : '' }}>Obesitas</option>
                            <option value="stunting" {{ $anak->status_gizi == 'stunting' ? 'selected' : '' }}>Stunting</option>
                            <option value="wasting" {{ $anak->status_gizi == 'wasting' ? 'selected' : '' }}>Wasting</option>
                            <option value="underweight" {{ $anak->status_gizi == 'underweight' ? 'selected' : '' }}>Underweight</option>
                        </select>
                    </div>

                    {{-- ACTION BUTTON --}}
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('data-anak.index') }}" class="btn btn-light btn-lg px-4">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>

                        <button type="submit" class="btn btn-warning btn-lg px-4 text-white">
                            <i class="mdi mdi-content-save-edit"></i> Update Data
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
