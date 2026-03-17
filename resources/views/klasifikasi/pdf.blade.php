<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hasil Klasifikasi - {{ $data->nama_prediksi }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        h3, h4 { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        th { background-color: #f2f2f2; }
        .section-title { margin-top: 20px; margin-bottom: 5px; font-weight: bold; }
    </style>
</head>
<body>

<h3>Hasil Klasifikasi Status Gizi Balita</h3>
<p><strong>Nama Klasifikasi:</strong> {{ $data->nama_prediksi }}</p>
<p><strong>Model yang Digunakan:</strong> {{ $data->modelML->nama_model ?? $data->model }}</p>
<p><strong>Dicetak pada:</strong> {{ now()->format('d-m-Y H:i:s') }}</p>

<h4 class="section-title">Input Data & Klasifikasi:</h4>
@php
    $inputs = json_decode($data->input_data, true) ?: [];
    $kelas  = json_decode($data->kelas, true) ?: [];
    $probs  = json_decode($data->probabilitas, true) ?: [];
@endphp

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Jenis Kelamin</th>
            <th>Usia (Bulan)</th>
            <th>Berat (kg)</th>
            <th>Tinggi (cm)</th>
            <th>IMT</th>
            <th>Klasifikasi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($inputs as $idx => $inp)
        <tr>
            <td>{{ $idx + 1 }}</td>
            <td>{{ $inp['jenis_kelamin'] ?? '-' }}</td>
            <td>{{ $inp['usia'] ?? '-' }}</td>
            <td>{{ $inp['berat'] ?? '-' }}</td>
            <td>{{ $inp['tinggi'] ?? '-' }}</td>
            <td>{{ $inp['imt'] ?? '-' }}</td>
            <td>{{ $kelas[$idx] ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h4 class="section-title">Probabilitas (%):</h4>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Normal</th>
            <th>Wasting</th>
            <th>Underweight</th>
            <th>Overweight</th>
            <th>Obesitas</th>
        </tr>
    </thead>
    <tbody>
        @foreach($probs as $idx => $p)
        <tr>
            <td>{{ $idx + 1 }}</td>
            <td>{{ number_format($p['normal'] ?? 0, 1) }}%</td>
            <td>{{ number_format($p['wasting'] ?? 0, 1) }}%</td>
            <td>{{ number_format($p['underweight'] ?? 0, 1) }}%</td>
            <td>{{ number_format($p['overweight'] ?? 0, 1) }}%</td>
            <td>{{ number_format($p['obesitas'] ?? 0, 1) }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
