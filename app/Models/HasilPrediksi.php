<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilPrediksi extends Model
{
    protected $table = 'hasil_prediksi';

    protected $fillable = [
        'nama_prediksi',
        'model',
        'kelas',
        'input_data',
        'probabilitas',
    ];

    protected $casts = [
        'input_data' => 'array',
        'probabilitas' => 'array',
    ];

        /**
     * Relasi ke ModelML berdasarkan file_model
     */
    public function modelML()
    {
        return $this->belongsTo(ModelML::class, 'model', 'file_model');
    }
}

