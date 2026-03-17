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
     * Halaman Form Prediksi
     */
    public function index(Request $request)
    {
        $models = ModelML::orderBy('created_at','desc')->get();

        $selected_model_file = $request->model_file
            ?? ($models->first()->file_model ?? null);

        return view('klasifikasi.prediksi',[
            'models' => $models,
            'selected_model_file' => $selected_model_file
        ]);
    }


    /**
     * Proses Prediksi ke Flask API
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


        // ================================
        // CEK MODEL
        // ================================
        $model = ModelML::where('file_model',$request->model_file)->first();

        if(!$model){
            return back()->with('error','Model tidak ditemukan');
        }

        if(!Storage::exists($request->model_file)){
            return back()->with('error','File model tidak ada di storage');
        }


        // ================================
        // LOAD MODEL
        // ================================
        $model_base64 = base64_encode(
            Storage::get($request->model_file)
        );


        // ================================
        // FORMAT DATA UNTUK FLASK
        // ================================
        $dataset = [];

        $jumlah = count($request->usia);

        for($i=0; $i<$jumlah; $i++){

            $dataset[] = [
                'jenis_kelamin' => $request->jenis_kelamin[$i],
                'usia_bulan'    => (float)$request->usia[$i],
                'berat_badan'   => (float)$request->berat[$i],
                'tinggi_badan'  => (float)$request->tinggi[$i],
                'imt'           => (float)$request->imt[$i],
            ];
        }


        // ================================
        // CALL FLASK API
        // ================================
        $client = new Client([
            'timeout' => 120
        ]);

        try{

            $response = $client->post('http://127.0.0.1:5000/predict',[
                'json' => [
                    'model_base64' => $model_base64,
                    'data' => $dataset
                ]
            ]);

            $json = json_decode($response->getBody(), true);

        }
        catch(\Exception $e){

            return back()->with('error',
                'Flask API tidak merespon : '.$e->getMessage()
            );

        }


        if(!isset($json['predictions'])){
            return back()->with('error','Respon Flask tidak valid');
        }


        // ================================
        // FORMAT HASIL
        // ================================
        $results = [];

        foreach($json['predictions'] as $i => $row){

            $results[] = [

                'no' => $i+1,

                'input' => [
                    'jenis_kelamin' => $dataset[$i]['jenis_kelamin'],
                    'usia' => $dataset[$i]['usia_bulan'],
                    'berat' => $dataset[$i]['berat_badan'],
                    'tinggi'=> $dataset[$i]['tinggi_badan'],
                    'imt'   => $dataset[$i]['imt']
                ],

                'hasil' => $row['prediction'] ?? 'Error',

                'probabilitas' => $row['probability'] ?? []
            ];

        }


        $execution_time = $json['execution_time'] ?? 0;


        // ================================
        // SIMPAN SESSION
        // ================================
        session([

            'result' => $results,

            'model_info' => [
                'nama_model' => $model->nama_model,
                'algoritma' => $model->algoritma,
                'akurasi' => $model->akurasi,
                'execution_time' => $execution_time
            ],

            'nama_prediksi' => $request->nama_prediksi,
            'model_file' => $request->model_file

        ]);


        return redirect()->route('prediksi.index')
                ->with('success','Prediksi berhasil dijalankan');

    }



    /**
     * Simpan hasil ke database
     */
    public function save(Request $request)
    {

        $request->validate([
            'nama_prediksi'=>'required|string',
            'model'=>'required|string',
            'data'=>'required'
        ]);


        $rows = json_decode($request->data,true);

        if(!$rows){
            return back()->with('error','Data prediksi tidak valid');
        }


        HasilPrediksi::create([

            'nama_prediksi' => $request->nama_prediksi,

            'model' => $request->model,

            'kelas' => json_encode(
                array_column($rows,'hasil'),
                JSON_UNESCAPED_UNICODE
            ),

            'input_data' => json_encode(
                array_column($rows,'input'),
                JSON_UNESCAPED_UNICODE
            ),

            'probabilitas' => json_encode(
                array_column($rows,'probabilitas'),
                JSON_UNESCAPED_UNICODE
            ),

        ]);


        return redirect()
            ->route('hasil.index')
            ->with('success','Hasil prediksi berhasil disimpan');

    }



    /**
     * Hapus session hasil
     */
    public function clear()
    {

        session()->forget([
            'result',
            'model_info',
            'nama_prediksi',
            'model_file'
        ]);

        return redirect()->route('prediksi.index')
                ->with('success','Hasil prediksi dihapus');

    }



    /**
     * Halaman hasil prediksi
     */
    public function hasil(Request $request)
    {

        $ids = $request->ids
            ? explode(',',$request->ids)
            : null;

        if($ids){

            $hasil = HasilPrediksi::whereIn('id',$ids)
                    ->orderBy('created_at','desc')
                    ->get();

        }else{

            $hasil = HasilPrediksi::orderBy('created_at','desc')
                    ->get();
        }


        return view('klasifikasi.hasil',[
            'hasil'=>$hasil
        ]);

    }

}
