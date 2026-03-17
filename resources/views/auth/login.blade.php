<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | Sistem Klasifikasi Gizi Balita</title>

<!-- Fonts & Icons -->
<link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body.login-page {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #f0f2f5;
    font-family: 'Segoe UI', sans-serif;
    color: #333;
}
.login-wrapper {
    display: flex;
    width: 900px;
    max-width: 95%;
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}
.login-container, .welcome-card {
    flex: 1;
    padding: 50px;
}
.login-container {
    background: #ffffff;
}
.login-title {
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
.btn-login {
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
.btn-login:hover {
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
.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
</style>
</head>
<body class="login-page">
<div class="login-wrapper">

    <!-- Form Login -->
    <div class="login-container">
        <h2 class="login-title">Login</h2>

        {{-- Menampilkan pesan sukses setelah register --}}
        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Menampilkan error dari validasi Laravel --}}
        @if ($errors->any())
            <div class="alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Menampilkan session error --}}
        @if(session('error'))
            <div class="alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <input type="email" name="email" placeholder="Email" class="form-input" value="{{ old('email') }}" required autofocus>
            <input type="password" name="password" placeholder="Password" class="form-input" required>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>
                <a href="#" class="text-sm text-white fw-semibold">Lupa password?</a>
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>
    </div>

    <!-- Welcome Card -->
    <div class="welcome-card">
        <h2>Selamat Datang!</h2>
        <p>
            Halo, selamat datang kembali di <b>Sistem Klasifikasi Status Gizi Balita</b>!<br>
            Gunakan sistem ini untuk mengklasifikasikan status gizi balita dengan akurat<br>
            menggunakan model <b>Random Forest</b> yang telah dilatih.
        </p>
        <a href="{{ route('register') }}" class="btn-welcome">Belum punya akun? Daftar</a>
    </div>

</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
