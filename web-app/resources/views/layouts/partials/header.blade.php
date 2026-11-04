<header class="app-header">
  <nav class="navbar navbar-expand navbar-light px-3 py-2 w-100 d-flex justify-content-between align-items-center">
    
    <!-- Sisi Kiri Header: Toggle Mobile & Badge Sekolah -->
    <div class="d-flex align-items-center gap-2">
      <button class="nav-link sidebartoggler nav-icon-hover d-block d-xl-none p-1 border-0 bg-transparent cursor-pointer" id="headerCollapse" type="button">
        <i class="ti ti-menu-2 fs-6"></i>
      </button>

      <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fs-2 fw-semibold d-flex align-items-center">
        <iconify-icon icon="solar:map-point-wave-linear" class="me-1 fs-4"></iconify-icon> SMK Negeri 1 Bungo - Jambi
      </span>

      <span class="badge bg-light text-muted px-3 py-2 rounded-pill fs-2 d-none d-md-inline-block">
        Tahun Ajaran 2023/2024
      </span>
    </div>

    <!-- Sisi Kanan Header: Mode Single Admin & Profil Pengguna -->
    <div class="d-flex align-items-center gap-3">
      <span class="badge bg-success-subtle text-success border border-success-subtle py-2 px-3 rounded-pill fs-2 d-flex align-items-center">
        <iconify-icon icon="solar:check-circle-bold" class="me-1 fs-3"></iconify-icon> Mode: Single Admin
      </span>

      <!-- Dropdown Profil Admin -->
      <div class="dropdown">
        <a class="d-flex align-items-center gap-2 text-decoration-none p-1 rounded-3 profile-btn" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown" aria-expanded="false">
          <div class="text-end pe-1">
            <span class="d-block fw-bold text-dark fs-3 lh-sm text-nowrap">Administrator Sistem Cerdas</span>
            <small class="text-primary fw-semibold fs-1 text-uppercase text-nowrap">Admin Utama</small>
          </div>
          <img src="{{ asset('assets/images/profile/user-1.jpg') }}" alt="Profile" width="40" height="40" class="rounded-circle border border-2 border-primary-subtle flex-shrink-0">
          <iconify-icon icon="solar:alt-arrow-down-linear" class="fs-4 text-muted"></iconify-icon>
        </a>

        <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-2 mt-2" aria-labelledby="drop2" style="min-width: 250px;">
          <div class="px-3 py-2 border-bottom mb-2 bg-light rounded-2">
            <p class="mb-0 fs-3 fw-bold text-dark">Administrator Sistem Cerdas</p>
            <small class="text-muted">{{ Auth::user()->email ?? 'admin@smkn1bungo.sch.id' }}</small>
          </div>
          <a class="dropdown-item py-2 px-3 rounded d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            <iconify-icon icon="solar:user-linear" class="fs-5 text-primary"></iconify-icon> Dashboard Profil
          </a>
          <hr class="my-1">
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center py-2 rounded-2 fw-semibold">
              <iconify-icon icon="solar:logout-2-linear" class="me-2 fs-5"></iconify-icon> Logout
            </button>
          </form>
        </div>
      </div>
    </div>

  </nav>
</header>
