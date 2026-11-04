<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Sistem Cerdas Deteksi Dini Risiko Putus Sekolah - SMKN 1 Bungo')</title>
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
  <!-- MatDash Core Stylesheet -->
  <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/icons/tabler-icons/tabler-icons.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/libs/apexcharts/dist/apexcharts.css') }}" />

  <style>
    /* Custom Clean Polish */
    body {
      background-color: #f4f6f9;
      font-family: inherit;
    }
    .left-sidebar {
      background: #ffffff;
      box-shadow: 0 0 20px rgba(0,0,0,0.05);
      border-right: 1px solid #eef2f6;
    }
    .brand-logo {
      padding: 20px 24px;
      border-bottom: 1px solid #f1f4f9;
      min-height: 70px;
    }
    .app-header {
      background: #ffffff;
      border-bottom: 1px solid #eef2f6;
      box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }
    .card {
      border: 1px solid #eef2f6;
      border-radius: 12px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.03);
      margin-bottom: 24px;
    }
    .card-title {
      font-weight: 700;
      color: #1a202c;
    }
    .card-subtitle {
      color: #718096;
      font-size: 0.875rem;
    }
    .table thead th {
      background-color: #f8fafc;
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #475569;
      border-bottom: 2px solid #e2e8f0;
      font-weight: 600;
    }
    .table-hover tbody tr:hover {
      background-color: #f8fafc;
    }
    .sidebar-nav ul .sidebar-item .sidebar-link {
      color: #4a5568;
      font-weight: 500;
      border-radius: 8px;
      margin: 4px 12px;
      padding: 10px 14px;
      display: flex;
      align-items: center;
      transition: all 0.2s ease-in-out;
    }
    .sidebar-nav ul .sidebar-item .sidebar-link i,
    .sidebar-nav ul .sidebar-item .sidebar-link iconify-icon {
      font-size: 1.25rem;
      margin-right: 12px;
      color: #718096;
    }
    .sidebar-nav ul .sidebar-item .sidebar-link:hover {
      background: #f0f4ff;
      color: #5d87ff;
    }
    .sidebar-nav ul .sidebar-item .sidebar-link:hover i,
    .sidebar-nav ul .sidebar-item .sidebar-link:hover iconify-icon {
      color: #5d87ff;
    }
    .sidebar-nav ul .sidebar-item.selected > .sidebar-link {
      background: #5d87ff;
      color: #ffffff !important;
    }
    .sidebar-nav ul .sidebar-item.selected > .sidebar-link i,
    .sidebar-nav ul .sidebar-item.selected > .sidebar-link iconify-icon {
      color: #ffffff !important;
    }
    .nav-small-cap {
      color: #a0aec0;
      font-size: 0.75rem;
      font-weight: 700;
      padding: 12px 24px 6px;
      letter-spacing: 0.8px;
    }
    .badge {
      font-weight: 600;
      border-radius: 6px;
      padding: 6px 10px;
    }
    .progress {
      border-radius: 6px;
      background-color: #edf2f7;
    }
  </style>

  @stack('styles')
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
    
    <!-- Sidebar Start -->
    @include('layouts.partials.sidebar')
    <!-- Sidebar End -->

    <!-- Main wrapper -->
    <div class="body-wrapper">
      
      <!-- Header Start -->
      @include('layouts.partials.header')
      <!-- Header End -->

      <div class="container-fluid py-4 px-4">
        <!-- Alert Messages -->
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
              <i class="ti ti-circle-check fs-6 me-2"></i>
              <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
              <i class="ti ti-alert-triangle fs-6 me-2"></i>
              <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        @yield('content')

        <!-- Footer -->
        @include('layouts.partials.footer')
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/libs/simplebar/dist/simplebar.js') }}"></script>
  <script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
  <script src="{{ asset('assets/js/app.min.js') }}"></script>
  <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  @stack('scripts')
</body>
</html>
