@extends('layouts.app')

@section('content')
<div class="container">

    {{-- Header Welcome --}}
    <div class="p-4 rounded mb-4 shadow-sm"
        style="background: linear-gradient(90deg, #4e73df, #1cc88a); color: white;">
        <h3 class="fw-bold mb-1">Welcome to Website Classification Dashboard</h3>
        <p class="mb-0">Kelola dataset, latih model, dan lakukan prediksi untuk status gizi anak.</p>
    </div>

    <div class="row g-4">

        {{-- CARD: Total Data Anak --}}
        <div class="col-md-4">
            <div class="card dashboard-card shadow-sm card-hover border-0 p-4"
                style="border-top: 4px solid #4e73df;">
                <div class="d-flex justify-content-between h-100">
                    <div class="card-content">
                        <h6 class="text-muted">Total Data Anak</h6>
                        <h3 class="fw-bold text-dark">{{ $totalDataAnak }}</h3>

                        <p class="small text-muted mt-2">
                            Jumlah total data anak yang telah dimasukkan ke sistem.  
                            Data ini menjadi dasar proses training model ML.
                        </p>
                    </div>
                    <i class="mdi mdi-account-group text-primary card-icon"></i>
                </div>
            </div>
        </div>

        {{-- CARD: Total Model --}}
        <div class="col-md-4">
            <div class="card dashboard-card shadow-sm card-hover border-0 p-4"
                style="border-top: 4px solid #1cc88a;">
                <div class="d-flex justify-content-between h-100">
                    <div class="card-content">
                        <h6 class="text-muted">Total Model</h6>
                        <h3 class="fw-bold text-dark">{{ $totalModel }}</h3>

                        <p class="small text-muted mt-2">
                            Jumlah model ML yang telah dilatih dan tersimpan.
                        </p>
                    </div>
                    <i class="mdi mdi-brain text-success card-icon"></i>
                </div>
            </div>
        </div>

        {{-- CARD: Model Terbaik --}}
        <div class="col-md-4">
            <div class="card dashboard-card shadow-sm card-hover border-0 p-4"
                style="border-top: 4px solid #36b9cc;">
                <div class="d-flex justify-content-between h-100">
                    <div class="card-content">
                        <h6 class="text-muted">Model Terbaik</h6>
                        <h4 class="fw-bold text-dark">{{ $modelTerbaik }}</h4>

                        <p class="small text-muted mt-2">
                            Model dengan akurasi tertinggi, direkomendasikan untuk klasifikasi.
                        </p>
                    </div>
                    <i class="mdi mdi-trophy-award text-info card-icon"></i>
                </div>
            </div>
        </div>

        {{-- CARD: Total Klasifikasi --}}
        <div class="col-md-6">
            <div class="card dashboard-card shadow-sm card-hover border-0 p-4"
                style="border-top: 4px solid #f6c23e;">
                <div class="d-flex justify-content-between h-100">
                    <div class="card-content">
                        <h6 class="text-muted">Total Klasifikasi</h6>
                        <h3 class="fw-bold text-dark">{{ $totalPrediksi }}</h3>

                        <p class="small text-muted mt-2">
                            Total klasifikasi status gizi yang telah dilakukan.
                        </p>
                    </div>
                    <i class="mdi mdi-chart-line text-warning card-icon"></i>
                </div>
            </div>
        </div>

        {{-- CARD: Akurasi Model Terakhir --}}
        <div class="col-md-6">
            <div class="card dashboard-card shadow-sm card-hover border-0 p-4"
                style="border-top: 4px solid #e74a3b;">
                <div class="d-flex justify-content-between h-100">
                    <div class="card-content">
                        <h6 class="text-muted">Akurasi Model Terbaru</h6>
                        <h3 class="fw-bold text-dark">
                            {{ isset($akurasiTerakhir) ? round($akurasiTerakhir * 100, 2).'%' : '-' }}
                        </h3>

                        <p class="small text-muted mt-2">
                            Akurasi model terbaru yang telah diselesaikan pelatihannya.
                        </p>
                    </div>
                    <i class="mdi mdi-target text-danger card-icon"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Recent Activity Section --}}
    <div class="card shadow-sm p-4 mt-4">
        <h5 class="fw-bold mb-3">Recent Activities</h5>

        @if(count($recentActivities) == 0)
            <p class="text-muted">Belum ada aktivitas.</p>
        @else
            <ul class="list-group">
                @foreach($recentActivities as $act)
                    <li class="list-group-item">
                        <i class="mdi mdi-clock-outline text-primary"></i> {{ $act }}
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

</div>

{{-- Custom CSS --}}
<style>
/* Semua card jadi ukuran sama */
.dashboard-card {
    min-height: 230px; /* Tinggi seragam */
    display: flex;
}

/* Biar icon selalu tampil cantik */
.card-icon {
    font-size: 45px;
    opacity: 0.85;
}

/* Hover effect */
.card-hover {
    transition: 0.25s;
}
.card-hover:hover {
    transform: translateY(-6px);
    box-shadow: 0px 10px 30px rgba(0,0,0,0.12);
}

/* Agar teks rapi */
.card-content {
    max-width: 75%;
}
</style>

@endsection
