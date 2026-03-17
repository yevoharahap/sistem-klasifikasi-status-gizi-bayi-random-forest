<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelML extends Model
{
    protected $table = 'models';

protected $fillable = [
    'nama_model',
    'algoritma',
    'akurasi',
    'parameter',
    'file_model', // tambahkan ini
];

    protected $casts = [
        'parameter' => 'array'
    ];
}
