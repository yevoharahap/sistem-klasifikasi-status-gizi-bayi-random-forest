<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Sistem Klasifikasi Status Gizi Balita</title>

  <!-- base:css -->
  <link rel="stylesheet" href="{{ asset('assets/template/vendors/mdi/css/materialdesignicons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/template/vendors/css/vendor.bundle.base.css') }}">
  <!-- endinject -->

  <!-- inject:css -->
  <link rel="stylesheet" href="{{ asset('assets/template/css/style.css') }}">
  <!-- endinject -->

  <link rel="shortcut icon" href="{{ asset('assets/template/images/favicon.png') }}" />
</head>

<body>
<div class="container-scroller d-flex">

    <!-- SIDEBAR -->
    <nav class="sidebar sidebar-offcanvas" id="sidebar">
      <ul class="nav">

        <!-- Dashboard -->
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="mdi mdi-view-quilt menu-icon"></i>
            <span class="menu-title">Dashboard</span>
            <div class="badge badge-info badge-pill">2</div>
          </a>
        </li>

        <!-- Management Data -->
        <li class="nav-item sidebar-category">
          <p>Management Data</p>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('data-anak.*') ? 'active' : '' }}" href="{{ route('data-anak.index') }}">
            <i class="mdi mdi-account-child menu-icon"></i>
            <span class="menu-title">Dataset</span>
          </a>
        </li>

        <!-- Data Analytics -->
        <li class="nav-item sidebar-category">
          <p>Data Analytics</p>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('klasifikasi.*') ? 'active' : '' }}" href="{{ route('klasifikasi.index') }}">
            <i class="mdi mdi-chart-pie menu-icon"></i>
            <span class="menu-title">Random Forest</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('models.*') ? 'active' : '' }}" href="{{ route('models.index') }}">
            <i class="mdi mdi-database menu-icon"></i>
            <span class="menu-title">Model</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('prediksi.index') ? 'active' : '' }}" href="{{ route('prediksi.index') }}">
            <i class="mdi mdi-chart-line menu-icon"></i>
            <span class="menu-title">Klasifikasi Gizi</span>
          </a>
        </li>

        <!-- Report -->
        <li class="nav-item sidebar-category">
          <p>Report</p>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('hasil.index') ? 'active' : '' }}" href="{{ route('hasil.index') }}">
            <i class="mdi mdi-grid-large menu-icon"></i>
            <span class="menu-title">Hasil Klasifikasi</span>
          </a>
        </li>

        <!-- Management User -->
        <li class="nav-item sidebar-category">
          <p>Management User</p>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('user.*') ? 'active' : '' }}" href="{{ route('user.index') }}">
            <i class="mdi mdi-file-document-box-outline menu-icon"></i>
            <span class="menu-title">Data User</span>
          </a>
        </li>

      </ul>
    </nav>
    <!-- END SIDEBAR -->

    <!-- PAGE BODY WRAPPER -->
    <div class="container-fluid page-body-wrapper">

      <!-- NAVBAR -->
      <nav class="navbar col-lg-12 col-12 px-0 py-0 py-lg-4 d-flex flex-row">

        <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">

<div class="navbar-brand-wrapper">
  <a class="navbar-brand brand-logo" href="{{ url('/') }}">
    <img src="{{ asset('assets/template/images/other/logo.svg') }}" alt="logo.svg" width="60" height="60"/>
  </a>
  <a class="navbar-brand brand-logo-mini" href="{{ url('/') }}">
    <img src="{{ asset('assets/template/images/other/logo-mini.svg') }}" alt="logo-mini.svg" width="40" height="40"/>
  </a>
</div>


          <h3 class="font-weight-bold mb-0 d-none d-md-block mt-1">
            Sistem Klasifikasi Status Gizi Balita - Penerapan Random Forest
          </h3>

          <ul class="navbar-nav navbar-nav-right">
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
              <span class="mdi mdi-menu"></span>
            </button>
          </ul>
        </div>

        <div class="navbar-menu-wrapper navbar-search-wrapper d-none d-lg-flex align-items-center">

          <ul class="navbar-nav mr-lg-2">
            <li class="nav-item nav-search d-none d-lg-block">
              <div class="input-group">
                <input type="text" class="form-control" placeholder="Search Here..." aria-label="search" aria-describedby="search">
              </div>
            </li>
          </ul>

          <ul class="navbar-nav navbar-nav-right">

            <!-- User Profile & Logout -->
            <li class="nav-item nav-profile dropdown">
              <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ asset('assets/template/images/faces/face5.jpg') }}" alt="profile" class="rounded-circle me-2" width="35" height="35"/>
                <span class="nav-profile-name">{{ Auth::user()->name }}</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end navbar-dropdown shadow-sm" aria-labelledby="profileDropdown">
                <li>
                  <a class="dropdown-item d-flex align-items-center" href="{{ route('user.edit', Auth::user()->id) }}">
                    <i class="mdi mdi-settings text-primary me-2"></i>
                    Settings
                  </a>
                </li>
                <li>
                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item d-flex align-items-center">
                      <i class="mdi mdi-logout text-primary me-2"></i>
                      Logout
                    </button>
                  </form>
                </li>
              </ul>
            </li>

          </ul>

        </div>
      </nav>
      <!-- END NAVBAR -->

      <!-- MAIN PANEL -->
      <div class="main-panel">
        <div class="content-wrapper">
          {{-- ISI HALAMAN DINAMIS --}}
          @yield('content')
        </div>

        <!-- FOOTER -->
        <footer class="footer">
          <div class="card">
            <div class="card-body">
              <div class="d-sm-flex justify-content-center justify-content-sm-between py-2">
                <span class="text-muted">
                  © {{ date('Y') }} Sistem Klasifikasi Status Gizi Anak | Random Forest
                </span>
                <span class="text-muted">
                  Teknik Informatika
                </span>
              </div>
            </div>
          </div>
        </footer>
        <!-- END FOOTER -->

      </div>
      <!-- END MAIN PANEL -->

    </div>
    <!-- END PAGE BODY WRAPPER -->

</div>

<!-- JS -->
<script src="{{ asset('assets/template/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('assets/template/vendors/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('assets/template/js/off-canvas.js') }}"></script>
<script src="{{ asset('assets/template/js/hoverable-collapse.js') }}"></script>
<script src="{{ asset('assets/template/js/template.js') }}"></script>

@stack('scripts')

</body>
</html>
