<aside class="left-sidebar">
  <div>
    <div class="brand-logo d-flex align-items-center justify-content-between">
      <a href="{{ route('dashboard') }}" class="text-nowrap logo-img d-flex align-items-center text-decoration-none">
        <div class="d-flex align-items-center gap-2">
          <span class="p-2 rounded bg-primary text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
            <iconify-icon icon="solar:shield-check-bold" class="fs-6"></iconify-icon>
          </span>
          <div>
            <h6 class="fw-bolder mb-0 text-primary fs-4" style="letter-spacing: -0.3px;">EWS SMKN 1 BUNGO</h6>
            <small class="text-muted fs-1 text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Sistem Cerdas</small>
          </div>
        </div>
      </a>
      <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
        <i class="ti ti-x fs-8"></i>
      </div>
    </div>

    <!-- Sidebar navigation-->
    <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
      <ul id="sidebarnav" class="pt-2">
        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">BERANDA &amp; MONITORING</span>
        </li>
        <li class="sidebar-item {{ request()->routeIs('dashboard') ? 'selected' : '' }}">
          <a class="sidebar-link" href="{{ route('dashboard') }}" aria-expanded="false">
            <iconify-icon icon="solar:chart-square-linear"></iconify-icon>
            <span class="hide-menu">Dashboard EWS</span>
          </a>
        </li>

        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">DATA MASTER</span>
        </li>
        <li class="sidebar-item {{ request()->routeIs('siswa.*') ? 'selected' : '' }}">
          <a class="sidebar-link" href="{{ route('siswa.index') }}" aria-expanded="false">
            <iconify-icon icon="solar:users-group-rounded-linear"></iconify-icon>
            <span class="hide-menu">Data Siswa (Dapodik)</span>
          </a>
        </li>
        <li class="sidebar-item {{ request()->routeIs('nilai.*') ? 'selected' : '' }}">
          <a class="sidebar-link" href="{{ route('nilai.index') }}" aria-expanded="false">
            <iconify-icon icon="solar:document-text-linear"></iconify-icon>
            <span class="hide-menu">Leger Nilai &amp; Presensi</span>
          </a>
        </li>

        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">DATA MINING &amp; SISTEM CERDAS</span>
        </li>
        <li class="sidebar-item {{ request()->routeIs('mining.*') ? 'selected' : '' }}">
          <a class="sidebar-link" href="{{ route('mining.index') }}" aria-expanded="false">
            <iconify-icon icon="solar:database-linear"></iconify-icon>
            <span class="hide-menu">Pipeline KDD &amp; Mining</span>
          </a>
        </li>
        <li class="sidebar-item {{ request()->routeIs('komparasi.*') ? 'selected' : '' }}">
          <a class="sidebar-link" href="{{ route('komparasi.index') }}" aria-expanded="false">
            <iconify-icon icon="solar:scale-linear"></iconify-icon>
            <span class="hide-menu">Komparasi RF vs NB</span>
          </a>
        </li>
        <li class="sidebar-item {{ request()->routeIs('prediksi.index') ? 'selected' : '' }}">
          <a class="sidebar-link" href="{{ route('prediksi.index') }}" aria-expanded="false">
            <iconify-icon icon="solar:danger-triangle-linear"></iconify-icon>
            <span class="hide-menu">Hasil Deteksi Dini</span>
          </a>
        </li>
        <li class="sidebar-item {{ request()->routeIs('prediksi.simulasi*') ? 'selected' : '' }}">
          <a class="sidebar-link" href="{{ route('prediksi.simulasi') }}" aria-expanded="false">
            <iconify-icon icon="solar:calculator-minimalistic-linear"></iconify-icon>
            <span class="hide-menu">Kalkulator Simulasi</span>
          </a>
        </li>

        <li class="nav-small-cap">
          <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
          <span class="hide-menu">TINDAK LANJUT &amp; LAPORAN</span>
        </li>
        <li class="sidebar-item {{ request()->routeIs('intervensi.*') ? 'selected' : '' }}">
          <a class="sidebar-link" href="{{ route('intervensi.index') }}" aria-expanded="false">
            <iconify-icon icon="solar:hand-heart-linear"></iconify-icon>
            <span class="hide-menu">Intervensi Siswa</span>
          </a>
        </li>
        <li class="sidebar-item {{ request()->routeIs('laporan.*') ? 'selected' : '' }}">
          <a class="sidebar-link" href="{{ route('laporan.index') }}" aria-expanded="false">
            <iconify-icon icon="solar:printer-linear"></iconify-icon>
            <span class="hide-menu">Laporan &amp; Export</span>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>
