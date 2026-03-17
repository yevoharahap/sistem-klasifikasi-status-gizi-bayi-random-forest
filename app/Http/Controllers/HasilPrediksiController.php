<?php

namespace App\Http\Controllers;

use App\Models\HasilPrediksi;
use Illuminate\Http\Request;
use PDF;

class HasilPrediksiController extends Controller
{
    public function index()
    {
        // Ambil semua hasil prediksi
        $hasil = HasilPrediksi::orderBy('created_at', 'desc')->get();

        // Kirim ke view
        return view('klasifikasi.hasil', compact('hasil'));
    }

    public function delete($id)
    {
        HasilPrediksi::findOrFail($id)->delete();
        return back()->with('success', 'Data berhasil dihapus.');
    }

    public function pdf($id)
    {
        $data = HasilPrediksi::findOrFail($id);
        $pdf = PDF::loadView('klasifikasi.pdf', compact('data'));
        return $pdf->download('hasil_prediksi_'.$data->id.'.pdf');
    }
}
