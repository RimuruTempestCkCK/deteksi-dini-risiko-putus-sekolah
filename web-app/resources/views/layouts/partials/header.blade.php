<header class="app-header">
  <nav class="navbar navbar-expand-lg navbar-light px-3 py-2">
    <ul class="navbar-nav">
      <li class="nav-item d-block d-xl-none">
        <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
          <i class="ti ti-menu-2 fs-6"></i>
        </a>
      </li>
      <li class="nav-item d-none d-sm-block">
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fs-2 fw-semibold">
            <iconify-icon icon="solar:map-point-wave-linear" class="me-1 fs-4"></iconify-icon> SMK Negeri 1 Bungo - Jambi
          </span>
          <span class="badge bg-light text-muted px-3 py-2 rounded-pill fs-2">
            Tahun Ajaran 2023/2024
          </span>
        </div>
      </li>
    </ul>

    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
      <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end gap-3">
        <li class="nav-item">
          <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2 rounded fs-2">
            <iconify-icon icon="solar:check-circle-bold" class="me-1"></iconify-icon> Mode: Single Admin
          </span>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link nav-icon-hover d-flex align-items-center gap-2 p-1" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="text-end d-none d-md-block">
              <span class="d-block fw-bold text-dark fs-3 lh-1">{{ Auth::user()->name ?? 'Administrator' }}</span>
              <small class="text-muted fs-1 text-uppercase">Admin Sistem</small>
            </div>
            <img src="{{ asset('assets/images/profile/user-1.jpg') }}" alt="Profile" width="38" height="38" class="rounded-circle border border-2 border-primary-subtle">
          </a>
          <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up shadow-lg border-0 rounded-3 p-2" aria-labelledby="drop2" style="min-width: 220px;">
            <div class="px-3 py-2 border-bottom mb-2">
              <p class="mb-0 fs-3 fw-bold text-dark">{{ Auth::user()->name ?? 'Administrator' }}</p>
              <small class="text-muted">{{ Auth::user()->email ?? 'admin@smkn1bungo.sch.id' }}</small>
            </div>
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-danger-subtle text-danger w-100 d-flex align-items-center justify-content-center py-2 rounded-2 fw-semibold">
                <iconify-icon icon="solar:logout-2-linear" class="me-2 fs-5"></iconify-icon> Logout
              </button>
            </form>
          </div>
        </li>
      </ul>
    </div>
  </nav>
</header>
