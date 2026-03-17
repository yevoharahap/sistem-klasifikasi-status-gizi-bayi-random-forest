<?php

namespace App\Http\Controllers;

use App\Models\DataAnak;
use App\Models\ModelML;
use App\Models\HasilPrediksi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Total data anak
        $totalDataAnak = DataAnak::count();

        // Total model ML
        $totalModel = ModelML::count();

        // Model terbaik
        $bestModel = ModelML::orderByDesc('akurasi')->first();
        $modelTerbaik = $bestModel ? $bestModel->nama_model : '-';
        $akurasiTerbaik = $bestModel ? $bestModel->akurasi : 0;

        // Total prediksi
        $totalPrediksi = HasilPrediksi::count();

        // Akurasi model terakhir
        $lastModel = ModelML::latest('created_at')->first();
        $akurasiTerakhir = $lastModel ? $lastModel->akurasi : 0;
        $waktuTraining = $lastModel ? $lastModel->waktu_training : 0;
        $dataLatih = $lastModel ? $lastModel->jumlah_data_latih : 0;

        // Grafik distribusi status gizi
        $statusGiziCount = DataAnak::selectRaw('status_gizi, COUNT(*) as total')
            ->groupBy('status_gizi')
            ->pluck('total', 'status_gizi')
            ->toArray();

        // Log aktivitas (sementara dummy)
        $recentActivities = [
            "Sistem siap digunakan",
            "Menunggu aktivitas baru ..."
        ];

        return view('dashboard', compact(
            'totalDataAnak',
            'totalModel',
            'modelTerbaik',
            'akurasiTerbaik',
            'totalPrediksi',
            'akurasiTerakhir',
            'waktuTraining',
            'dataLatih',
            'statusGiziCount',
            'recentActivities'
        ));
    }
}
