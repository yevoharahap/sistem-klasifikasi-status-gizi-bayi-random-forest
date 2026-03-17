<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataAnak;
use App\Models\ModelML;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

class KlasifikasiController extends Controller
{
    /**
     * Tampilkan halaman klasifikasi
     */
    public function index()
    {
        return view('klasifikasi.klasifikasi');
    }

    /**
     * Proses training Random Forest (tanpa simpan DB)
     */
    public function train(Request $request)
    {
        $client = new Client(['timeout' => 180]);

        // Ambil dataset dari database
        $dataset = DataAnak::all()
            ->map(function ($item) {
                $berat  = (float) $item->berat_badan;
                $tinggi = (float) $item->tinggi_badan;

                if ($berat <= 0 || $tinggi <= 0) return null;

                $imt = (float) $item->imt;
                if ($imt <= 0) {
                    $tbMeter = $tinggi / 100;
                    $imt = round($berat / ($tbMeter * $tbMeter), 2);
                }

                return [
                    "jenis_kelamin" => $item->jenis_kelamin,
                    "usia_bulan"    => (float) $item->usia_bulan,
                    "berat_badan"   => $berat,
                    "tinggi_badan"  => $tinggi,
                    "imt"           => $imt,
                    "status_gizi"   => $item->status_gizi
                ];
            })
            ->filter()
            ->values()
            ->toArray();

        if (count($dataset) < 5) {
            return back()->withErrors([
                'api_error' => 'Data tidak mencukupi untuk melatih model (minimal 5 data valid)'
            ]);
        }

        try {
            // Kirim dataset ke API Flask
            $response = $client->post('http://127.0.0.1:5000/train', [
                'json' => [
                    'dataset'           => $dataset,
                    'test_size'         => ((float) $request->split) / 100,
                    'n_estimators'      => (int) $request->n_estimators,
                    'max_depth'         => $request->filled('max_depth') ? (int) $request->max_depth : null,
                    'min_samples_split' => (int) $request->min_samples_split,
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            if (!$result || isset($result['error'])) {
                return back()->withErrors([
                    'api_error' => $result['error'] ?? 'Response API tidak valid'
                ]);
            }

            // Simpan file model di storage
            $modelB64 = $result['model_base64'] ?? null;
            if (!$modelB64) {
                return back()->withErrors([
                    'api_error' => 'Model tidak diterima dari server Flask.'
                ]);
            }

            $modelName = "model_RF_" . date('Ymd_His') . ".pkl";
            $modelPath = "models/{$modelName}";
            Storage::put($modelPath, base64_decode($modelB64));

            // Tambahkan path model ke result
            $result['model_path'] = $modelPath;

            // Simpan semua parameter ke session supaya blade bisa akses
            session([
                'split'             => $request->split,
                'n_estimators'      => $request->n_estimators,
                'max_depth'         => $request->max_depth,
                'min_samples_split' => $request->min_samples_split,
            ]);

            // HASIL TRAINING disimpan permanen di session
            session(['results' => $result]);

            return back();

        } catch (\Exception $e) {
            return back()->withErrors([
                'api_error' => 'Error API: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Simpan model ke DB (klik "Simpan Model Ini")
     */
    public function saveModel(Request $request)
    {
        $validated = $request->validate([
            'nama_model' => 'required|string|max:255',
            'algoritma'  => 'required|string|max:255',
            'akurasi'    => 'required|numeric',
            'parameter'  => 'required',
            'file_model' => 'required|string'
        ]);

        $parameters = json_decode($validated['parameter'], true);

        // Pastikan file_model ada di storage
        if (!Storage::exists($validated['file_model'])) {
            return back()->withErrors(['api_error' => 'File model tidak ditemukan di storage.']);
        }

        // Simpan ke database
        ModelML::create([
            'nama_model' => $validated['nama_model'],
            'algoritma'  => $validated['algoritma'],
            'akurasi'    => $validated['akurasi'],
            'parameter'  => $parameters,
            'file_model' => $validated['file_model']
        ]);

        // Hapus session hasil training supaya blade bersih saat di-refresh
        $request->session()->forget('results');

        // Redirect ke halaman daftar model
        return redirect()->route('models.index')
                         ->with('success', 'Model berhasil disimpan!');
    }

    /**
     * Hapus hasil training sementara dari session
     */
    public function clearResults(Request $request)
    {
        $request->session()->forget('results');
        return back()->with('success', 'Hasil training berhasil dihapus.');
    }

    /**
     * Tampilkan semua model tersimpan (halaman model.blade.php)
     */
    public function models()
    {
        $models = ModelML::orderBy('created_at', 'desc')->get();
        return view('klasifikasi.model', compact('models'));
    }

    private function getTreeAnalysis($modelPath, $treeIndex)
    {
        $client = new Client(['timeout' => 120]);

        if (!Storage::exists($modelPath)) {
            throw new \Exception('File model tidak ditemukan.');
        }

        $modelB64 = base64_encode(Storage::get($modelPath));

        $response = $client->post('http://127.0.0.1:5000/tree_analysis', [
            'json' => [
                'model_base64' => $modelB64,
                'tree_index'   => $treeIndex
            ]
        ]);

        $analysis = json_decode($response->getBody(), true);

        if (!$analysis || isset($analysis['error'])) {
            throw new \Exception(
                $analysis['error'] ?? 'Gagal melakukan analisis pohon keputusan'
            );
        }

        return $analysis;
    }


public function treeAnalysis(Request $request)
{
    $request->validate([
        'tree_index' => 'required|integer|min:0'
    ]);

    $treeIndex = (int) $request->tree_index;

    // Ambil hasil training dari session
    $results = session('results');

    if (!$results || !isset($results['model_path'])) {
        return back()->withErrors([
            'api_error' => 'Model belum tersedia. Silakan lakukan training terlebih dahulu.'
        ]);
    }

    try {
        $analysis = $this->getTreeAnalysis(
            $results['model_path'],
            $treeIndex
        );

        $results['tree_analysis'] = [
            'tree_info'          => $analysis['tree_info'] ?? [],
            'feature_importance' => $analysis['feature_importance'] ?? [],
            'rules'              => $analysis['rules'] ?? [],
            'image_base64'       => $analysis['image_base64'] ?? null,
        ];

        session(['results' => $results]);

        return back();

    } catch (\Exception $e) {
        return back()->withErrors([
            'api_error' => $e->getMessage()
        ]);
    }
}


}
