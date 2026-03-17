<?php

namespace App\Http\Controllers;

use App\Models\ModelML;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModelController extends Controller
{
    // Tampilkan semua model
    public function index()
    {
        $models = ModelML::orderBy('created_at', 'desc')->get();
        return view('models.model', compact('models'));
    }

    // Simpan model baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_model'  => 'required|string|max:255',
            'algoritma'   => 'required|string|max:255',
            'akurasi'     => 'required|numeric',
            'parameter'   => 'required',
            'file_model'  => 'nullable|file', // opsional jika upload file
        ]);

        $parameters = json_decode($validated['parameter'], true);

        if (!is_array($parameters)) {
            return back()->with('error', 'Parameter model tidak valid.');
        }

        $fileModelPath = null;
        if ($request->hasFile('file_model')) {
            $fileModelPath = $request->file('file_model')->store('models');
        }

        ModelML::create([
            'nama_model' => $validated['nama_model'],
            'algoritma'  => $validated['algoritma'],
            'akurasi'    => $validated['akurasi'],
            'parameter'  => $parameters,
            'file_model' => $fileModelPath,
        ]);

        return redirect()
            ->route('models.index')
            ->with('success', 'Model berhasil disimpan!');
    }

    // Detail model
    public function show($id)
    {
        $model = ModelML::findOrFail($id);
        return view('models.detail', compact('model'));
    }

    // Hapus model
    public function destroy($id)
    {
        $model = ModelML::findOrFail($id);

        // Hapus file model jika ada
        if ($model->file_model && Storage::exists($model->file_model)) {
            Storage::delete($model->file_model);
        }

        $model->delete();

        return redirect()
            ->route('models.index')
            ->with('success', 'Model berhasil dihapus!');
    }
}
