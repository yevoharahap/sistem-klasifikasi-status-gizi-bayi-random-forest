<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataAnak extends Model
{
    use HasFactory;

    protected $table = 'data_anak';

    protected $fillable = [
        'jenis_kelamin',
        'usia_bulan',
        'berat_badan',
        'tinggi_badan',
        'imt',
        'status_gizi'
    ];
}
