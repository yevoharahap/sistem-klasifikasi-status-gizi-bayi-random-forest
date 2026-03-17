<?php

namespace App\Imports;

use App\Models\DataAnak;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DataAnakImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $tinggi_m = $row['tinggi_badan'] / 100;
        $imt = $row['berat_badan'] / ($tinggi_m * $tinggi_m);

        return new DataAnak([
            'jenis_kelamin' => $row['jenis_kelamin'],
            'usia_bulan'    => $row['usia_bulan'],
            'berat_badan'   => $row['berat_badan'],
            'tinggi_badan'  => $row['tinggi_badan'],
            'imt'           => $imt,
            'status_gizi'   => $row['status_gizi'],
        ]);
    }
}
