@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">

        {{-- ================= JUDUL ================= --}}
        <h3 class="fw-bold mb-1 text-dark">
        Data Anak 
    </h3>
        <p class="text-muted" style="font-size: 14px; line-height: 1.6;">
          Halaman ini menampilkan seluruh data anak yang digunakan sebagai bahan analisis 
          dan pelatihan model <strong>Random Forest</strong>. 
          Anda dapat menambah data, mengimpor dataset, menghapus data, serta 
          mengelola setiap entri secara langsung. 
          Data yang bersih dan akurat akan sangat membantu dalam meningkatkan 
          performa model klasifikasi status gizi.
        </p>

        {{-- ============ NOTIFIKASI SUKSES ============ --}}
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- ================= TOMBOL AKSI ================= --}}
        <div class="mb-3 d-flex flex-wrap gap-2">

          <a href="{{ route('data-anak.create') }}" class="btn btn-primary btn-sm">
            Tambah Data
          </a>

          <button type="button"
                  class="btn btn-success btn-sm"
                  data-bs-toggle="modal"
                  data-bs-target="#importModal">
            Import Excel / CSV
          </button>

          <form action="{{ route('data-anak.destroyAll') }}"
                method="POST"
                onsubmit="return confirm('Yakin ingin menghapus semua data anak?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm">
              Hapus Semua
            </button>
          </form>

        </div>

        {{-- ================= TABEL DATASET ================= --}}
        <div class="table-responsive">
          <table class="table table-bordered table-hover align-middle" style="font-size: 14px;">
            <thead class="table-light">
              <tr class="text-center">
                <th>No</th>
                <th>Jenis Kelamin</th>
                <th>Usia (Bulan)</th>
                <th>Berat Badan (kg)</th>
                <th>Tinggi Badan (cm)</th>
                <th>IMT</th>
                <th>Status Gizi</th>
                <th>Aksi</th>
              </tr>
            </thead>

            <tbody>
              @forelse ($dataAnak as $item)
              <tr class="text-center">
                <td>{{ $loop->iteration + (($dataAnak->currentPage() - 1) * $dataAnak->perPage()) }}</td>
                <td>{{ $item->jenis_kelamin }}</td>
                <td>{{ $item->usia_bulan }}</td>
                <td>{{ $item->berat_badan }}</td>
                <td>{{ $item->tinggi_badan }}</td>
                <td>{{ number_format($item->imt, 2) }}</td>
                <td>
                  <span class="badge bg-info text-dark">
                    {{ ucfirst($item->status_gizi) }}
                  </span>
                </td>

                <td>
                  <a href="{{ route('data-anak.edit', $item->id) }}"
                     class="btn btn-warning btn-sm">
                    Edit
                  </a>

                  <form action="{{ route('data-anak.destroy', $item->id) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('Yakin hapus data ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">
                      Hapus
                    </button>
                  </form>
                </td>
              </tr>

              @empty
              <tr>
                <td colspan="8" class="text-center text-muted py-3">
                  Data anak belum tersedia
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- PAGINATION --}}
        @if($dataAnak->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
          <div class="text-muted small">
            Menampilkan {{ $dataAnak->firstItem() }} - {{ $dataAnak->lastItem() }}
            dari {{ $dataAnak->total() }} data  
            (Halaman {{ $dataAnak->currentPage() }} dari {{ $dataAnak->lastPage() }})
          </div>

          <nav>
            <ul class="pagination mb-0">

              {{-- Previous --}}
              @if ($dataAnak->onFirstPage())
                <li class="page-item disabled"><span class="page-link">Previous</span></li>
              @else
                <li class="page-item"><a class="page-link" href="{{ $dataAnak->previousPageUrl() }}">Previous</a></li>
              @endif

              @php
                $start = max(1, $dataAnak->currentPage() - 2);
                $end = min($dataAnak->lastPage(), $dataAnak->currentPage() + 2);
              @endphp

              @for($i = $start; $i <= $end; $i++)
                @if($i == $dataAnak->currentPage())
                  <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                @else
                  <li class="page-item"><a class="page-link" href="{{ $dataAnak->url($i) }}">{{ $i }}</a></li>
                @endif
              @endfor

              {{-- Next --}}
              @if ($dataAnak->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $dataAnak->nextPageUrl() }}">Next</a></li>
              @else
                <li class="page-item disabled"><span class="page-link">Next</span></li>
              @endif

            </ul>
          </nav>
        </div>
        @endif

      </div>
    </div>
  </div>
</div>

{{-- ================= MODAL IMPORT (STYLE SELARAS) ================= --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content border-0 shadow-lg rounded-4">

      <form action="{{ route('data-anak.import') }}"
            method="POST"
            enctype="multipart/form-data">
        @csrf

        {{-- HEADER --}}
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold fs-4">
            <i class="mdi mdi-file-import text-primary me-1"></i>
            Import Data Anak
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        {{-- BODY --}}
        <div class="modal-body px-4">

          <p class="text-muted small mb-3">
            Unggah file Excel atau CSV yang berisi data anak untuk ditambahkan ke dataset.
            Pastikan format kolom sudah sesuai agar proses import berhasil tanpa error.
          </p>

          {{-- FILE INPUT --}}
          <div class="mb-3">
            <label class="form-label fw-semibold">Pilih File (.xlsx / .csv)</label>
            <input type="file" name="file" class="form-control form-control-lg"
                   accept=".xlsx,.csv" required>
          </div>

          {{-- FORMAT INFO --}}
          <div class="alert alert-light border shadow-sm small rounded-3 mb-0">
            <strong>Format Kolom Wajib:</strong>
            <div class="mt-1">
              <code>jenis_kelamin</code> |
              <code>usia_bulan</code> |
              <code>berat_badan</code> |
              <code>tinggi_badan</code> |
              <code>status_gizi</code>
            </div>
          </div>

        </div>

        {{-- FOOTER --}}
        <div class="modal-footer border-0 px-4 pb-4">
          <button type="button" class="btn btn-light btn-lg px-4"
                  data-bs-dismiss="modal">
            <i class="mdi mdi-close"></i> Batal
          </button>

          <button type="submit" class="btn btn-success btn-lg px-4">
            <i class="mdi mdi-file-check"></i> Import Data
          </button>
        </div>

      </form>

    </div>
  </div>
</div>


@endsection
