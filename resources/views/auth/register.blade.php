<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register | Sistem Klasifikasi Gizi Balita</title>

<!-- Fonts & Icons -->
<link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body.register-page {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #f0f2f5;
    font-family: 'Segoe UI', sans-serif;
    color: #333;
}
.register-wrapper {
    display: flex;
    width: 900px;
    max-width: 95%;
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}
.register-container, .welcome-card {
    flex: 1;
    padding: 50px;
}
.register-container {
    background: #ffffff;
}
.register-title {
    font-weight: 700;
    color: #223e9c;
    margin-bottom: 30px;
    text-align: center;
}
.form-input {
    width: 100%;
    padding: 12px 15px;
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    transition: all 0.3s ease;
}
.form-input:focus {
    border-color: #2f9d94;
    box-shadow: 0 0 10px rgba(47,157,148,0.3);
    outline: none;
}
.btn-register {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    background: linear-gradient(135deg, #223e9c, #2f9d94);
    color: #fff;
    font-weight: 600;
    transition: 0.3s;
    box-shadow: 0 5px 15px rgba(47,157,148,0.3);
}
.btn-register:hover {
    background: linear-gradient(135deg, #2f9d94, #223e9c);
    box-shadow: 0 8px 20px rgba(47,157,148,0.4);
}
.welcome-card {
    background: linear-gradient(135deg, #223e9c, #2f9d94);
    color: #fff;
    display: flex;
    flex-direction: column;
    justify-content: center;
    text-align: center;
}
.welcome-card h2 {
    font-size: 28px;
    margin-bottom: 15px;
}
.welcome-card p {
    font-size: 15px;
    line-height: 1.6;
}
.btn-welcome {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 25px;
    border: 2px solid #fff;
    border-radius: 50px;
    color: #fff;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
}
.btn-welcome:hover {
    background: #fff;
    color: #223e9c;
}
.alert-error, .alert-danger, .alert-success {
    margin-bottom: 15px;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 14px;
}
</style>
</head>
<body class="register-page">
<div class="register-wrapper">

    <!-- Form Register -->
    <div class="register-container">
        <h2 class="register-title">Register</h2>

        {{-- Menampilkan error validasi --}}
        @if ($errors->any())
            <div class="alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Menampilkan session error/success --}}
        @if(session('error'))
            <div class="alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <input type="text" name="name" placeholder="Nama Lengkap" class="form-input" value="{{ old('name') }}" required autofocus>
            <input type="email" name="email" placeholder="Email" class="form-input" value="{{ old('email') }}" required>
            <input type="password" name="password" placeholder="Password" class="form-input" required>
            <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" class="form-input" required>
            <button type="submit" class="btn-register">Daftar</button>
        </form>
    </div>

    <!-- Welcome Card -->
    <div class="welcome-card">
        <h2>Selamat Datang!</h2>
        <p>
            Buat akun baru untuk mengakses <b>Sistem Klasifikasi Status Gizi Balita</b>.<br>
            Dengan akun ini, Anda bisa melakukan klasifikasi status gizi anak menggunakan <b>Random Forest</b>.<br>
            Pastikan data anak yang dimasukkan akurat agar hasil klasifikasi tepat.
        </p>
        <a href="{{ route('login') }}" class="btn-welcome">Sudah punya akun? Login</a>
    </div>

</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
