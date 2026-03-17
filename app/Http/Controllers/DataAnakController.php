<?php

namespace App\Http\Controllers;

use App\Models\DataAnak;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DataAnakImport;

class DataAnakController extends Controller
{
    // =====================
    // INDEX
    // =====================
    public function index()
    {
        // Pagination 50 per halaman + urut terbaru
        $dataAnak = DataAnak::latest()->paginate(50);

        return view('data-anak.index', compact('dataAnak'));
    }

    // =====================
    // CREATE
    // =====================
    public function create()
    {
        return view('data-anak.create');
    }

    // =====================
    // STORE
    // =====================
    public function store(Request $request)
    {
        $request->validate([
            'jenis_kelamin' => 'required|in:L,P',
            'usia_bulan'    => 'required|numeric|min:0',
            'berat_badan'   => 'required|numeric|min:0',
            'tinggi_badan'  => 'required|numeric|min:0',
            'status_gizi'   => 'required|string',
        ]);

        // Hitung IMT
        $tinggi_m = $request->tinggi_badan / 100;
        $imt = $request->berat_badan / ($tinggi_m * $tinggi_m);

        DataAnak::create([
            'jenis_kelamin' => $request->jenis_kelamin,
            'usia_bulan'    => $request->usia_bulan,
            'berat_badan'   => $request->berat_badan,
            'tinggi_badan'  => $request->tinggi_badan,
            'imt'           => $imt,
            'status_gizi'   => $request->status_gizi,
        ]);

        return redirect()->route('data-anak.index')
            ->with('success', 'Data anak berhasil ditambahkan');
    }

    // =====================
    // EDIT
    // =====================
    public function edit($id)
    {
        // PAKAI $anak agar konsisten dengan blade
        $anak = DataAnak::findOrFail($id);

        return view('data-anak.edit', compact('anak'));
    }

    // =====================
    // UPDATE
    // =====================
    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_kelamin' => 'required|in:L,P',
            'usia_bulan'    => 'required|numeric|min:0',
            'berat_badan'   => 'required|numeric|min:0',
            'tinggi_badan'  => 'required|numeric|min:0',
            'status_gizi'   => 'required|string',
        ]);

        $anak = DataAnak::findOrFail($id);

        // Hitung ulang IMT
        $tinggi_m = $request->tinggi_badan / 100;
        $imt = $request->berat_badan / ($tinggi_m * $tinggi_m);

        $anak->update([
            'jenis_kelamin' => $request->jenis_kelamin,
            'usia_bulan'    => $request->usia_bulan,
            'berat_badan'   => $request->berat_badan,
            'tinggi_badan'  => $request->tinggi_badan,
            'imt'           => $imt,
            'status_gizi'   => $request->status_gizi,
        ]);

        return redirect()->route('data-anak.index')
            ->with('success', 'Data anak berhasil diperbarui');
    }

    // =====================
    // DELETE SINGLE
    // =====================
    public function destroy($id)
    {
        $anak = DataAnak::findOrFail($id);
        $anak->delete();

        return redirect()->route('data-anak.index')
            ->with('success', 'Data anak berhasil dihapus');
    }

    // =====================
    // DELETE ALL
    // =====================
    public function destroyAll()
    {
        DataAnak::truncate();

        return redirect()->route('data-anak.index')
            ->with('success', 'Semua data anak berhasil dihapus');
    }

    // =====================
    // IMPORT EXCEL / CSV
    // =====================
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        Excel::import(new DataAnakImport, $request->file('file'));

        return redirect()->route('data-anak.index')
            ->with('success', 'Data anak berhasil diimport');
    }
}
