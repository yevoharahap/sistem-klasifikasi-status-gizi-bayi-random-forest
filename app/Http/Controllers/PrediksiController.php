<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ModelML;
use App\Models\HasilPrediksi;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

class PrediksiController extends Controller
{
    /**
     * Halaman form prediksi
     */
    public function index(Request $request)
    {
        $models = ModelML::orderBy('created_at', 'desc')->get();

        $selected_model_file = $request->model_file
            ?? ($models->first()->file_model ?? null);

        return view('klasifikasi.prediksi', [
            'models'              => $models,
            'selected_model_file' => $selected_model_file,
            'result'              => session('result'),
            'model_info'          => session('model_info'),
            'nama_prediksi'       => session('nama_prediksi'),
        ]);
    }

    /**
     * Proses prediksi
     */
    public function predict(Request $request)
    {
        $request->validate([
            'model_file' => 'required|string',
            'jenis_kelamin'   => 'required|array',
            'jenis_kelamin.*' => 'required|in:L,P',
            'usia'   => 'required|array',
            'usia.*' => 'required|numeric',
            'berat'   => 'required|array',
            'berat.*' => 'required|numeric',
            'tinggi'   => 'required|array',
            'tinggi.*' => 'required|numeric',
            'imt'   => 'required|array',
            'imt.*' => 'required|numeric',
        ]);

        $model = ModelML::where('file_model', $request->model_file)->first();
        if (!$model) return back()->with('error', 'Model tidak ditemukan.');
        if (!Storage::exists($request->model_file)) return back()->with('error', 'File model tidak ditemukan.');

        $model_base64 = base64_encode(Storage::get($request->model_file));
        $client = new Client(['timeout' => 60]);
        $results = [];
        $execution_times = [];

        try {
            $jumlah = count($request->usia);
            for ($i = 0; $i < $jumlah; $i++) {
                $response = $client->post('http://127.0.0.1:5000/predict', [
                    'json' => [
                        'model_base64'  => $model_base64,
                        'jenis_kelamin' => $request->jenis_kelamin[$i],
                        'usia_bulan'    => (float) $request->usia[$i],
                        'berat_badan'   => (float) $request->berat[$i],
                        'tinggi_badan'  => (float) $request->tinggi[$i],
                        'imt'           => (float) $request->imt[$i],
                    ]
                ]);

                $json = json_decode($response->getBody(), true);

                $results[] = [
                    'no'    => $i + 1,
                    'input' => [
                        'jenis_kelamin' => $request->jenis_kelamin[$i],
                        'usia'          => $request->usia[$i],
                        'berat'         => $request->berat[$i],
                        'tinggi'        => $request->tinggi[$i],
                        'imt'           => $request->imt[$i],
                    ],
                    'hasil'        => $json['prediction'] ?? 'Error',
                    'probabilitas' => $json['probability'] ?? [],
                ];

                $execution_times[] = $json['execution_time'] ?? 0;
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memprediksi: ' . $e->getMessage());
        }

        $avg_time = count($execution_times) ? round(array_sum($execution_times) / count($execution_times), 4) : 0;

        // Simpan hasil prediksi session sebagai batch
        session([
            'result'       => $results,
            'model_info'   => [
                'nama_model'     => $model->nama_model,
                'algoritma'      => $model->algoritma,
                'akurasi'        => $model->akurasi,
                'execution_time' => $avg_time,
            ],
            'nama_prediksi' => $request->nama_prediksi,
            'model_file'    => $request->model_file,
        ]);

        return back()->with('success', 'Prediksi berhasil dijalankan.');
    }

    /**
     * Simpan hasil prediksi sebagai satu batch ke database
     */
    public function save(Request $request)
    {
        $request->validate([
            'nama_prediksi' => 'required|string',
            'model'         => 'required|string',
            'data'          => 'required'
        ]);

        $rows = json_decode($request->data, true);
        if (!$rows || !is_array($rows)) {
            return back()->with('error', 'Data prediksi tidak valid.');
        }

        // Simpan semua data prediksi dalam satu record
        HasilPrediksi::create([
            'nama_prediksi' => $request->nama_prediksi,
            'model'         => $request->model,
            'kelas'         => json_encode(array_column($rows, 'hasil'), JSON_UNESCAPED_UNICODE),
            'input_data'    => json_encode(array_column($rows, 'input'), JSON_UNESCAPED_UNICODE),
            'probabilitas'  => json_encode(array_column($rows, 'probabilitas'), JSON_UNESCAPED_UNICODE),
        ]);

        // Redirect langsung ke halaman hasil.blade.php (menampilkan semua hasil)
        return redirect()->route('hasil.index')
                        ->with('success', 'Hasil prediksi berhasil disimpan!');
    }


    /**
     * Hapus session prediksi
     */
    public function clear()
    {
        session()->forget(['result', 'model_info', 'nama_prediksi', 'model_file']);
        return back()->with('success', 'Hasil prediksi dihapus.');
    }

    /**
     * Halaman tampil hasil prediksi
     */
    public function hasil(Request $request)
    {
        $ids = $request->ids ? explode(',', $request->ids) : null;

        if ($ids) {
            // Jika ada parameter ids, tampilkan hanya yang baru disimpan
            $hasil = HasilPrediksi::whereIn('id', $ids)->orderBy('created_at', 'desc')->get();
        } else {
            // Jika tidak ada ids (klik sidebar), tampilkan semua hasil
            $hasil = HasilPrediksi::orderBy('created_at', 'desc')->get();
        }

        return view('klasifikasi.hasil', [
            'hasil' => $hasil
        ]);
    }
}
