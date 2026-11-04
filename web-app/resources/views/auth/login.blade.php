<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login Administrator - Sistem Cerdas EWS SMKN 1 Bungo</title>
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
  <style>
    body {
      background: linear-gradient(135deg, #f0f4ff 0%, #e2e8f0 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: inherit;
    }
    .auth-card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      background: #ffffff;
      overflow: hidden;
    }
    .auth-header {
      background: #5d87ff;
      color: #ffffff;
      padding: 32px 24px;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-7 col-lg-5 col-xl-4">
        <div class="card auth-card">
          <div class="auth-header">
            <div class="d-inline-flex p-3 rounded-circle bg-white text-primary shadow-sm mb-3">
              <iconify-icon icon="solar:shield-check-bold" class="fs-8"></iconify-icon>
            </div>
            <h4 class="fw-bolder text-white mb-1">SMK NEGERI 1 BUNGO</h4>
            <p class="mb-0 text-white text-opacity-75 fs-2">Sistem Cerdas Deteksi Dini Siswa Putus Sekolah</p>
          </div>

          <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
              <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fs-2 fw-semibold">
                Random Forest &amp; Naïve Bayes
              </span>
            </div>

            @if($errors->any())
              <div class="alert alert-danger py-2 px-3 border-0 rounded-3 mb-3">
                <small><i class="ti ti-alert-triangle me-1"></i> {{ $errors->first() }}</small>
              </div>
            @endif

            @if(session('info'))
              <div class="alert alert-info py-2 px-3 border-0 rounded-3 mb-3">
                <small>{{ session('info') }}</small>
              </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
              @csrf
              <div class="mb-3">
                <label for="username" class="form-label fw-semibold text-dark fs-3">Username / Email</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><iconify-icon icon="solar:user-linear" class="fs-5 text-muted"></iconify-icon></span>
                  <input type="text" class="form-control border-start-0" id="username" name="username" value="{{ old('username', 'admin') }}" required autofocus placeholder="Masukkan username admin">
                </div>
              </div>
              <div class="mb-4">
                <label for="password" class="form-label fw-semibold text-dark fs-3">Password</label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0"><iconify-icon icon="solar:lock-password-linear" class="fs-5 text-muted"></iconify-icon></span>
                  <input type="password" class="form-control border-start-0" id="password" name="password" value="admin123" required placeholder="Masukkan password">
                </div>
              </div>
              
              <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check">
                  <input class="form-check-input primary" type="checkbox" name="remember" id="remember" checked>
                  <label class="form-check-label text-dark fs-2" for="remember">
                    Ingat Sesi Saya
                  </label>
                </div>
              </div>

              <button type="submit" class="btn btn-primary w-100 py-2 fs-4 rounded-3 fw-bold shadow-sm d-flex align-items-center justify-content-center">
                <iconify-icon icon="solar:login-2-linear" class="me-2 fs-5"></iconify-icon> Masuk Sebagai Admin
              </button>

              <div class="mt-4 p-3 bg-light rounded-3 text-center border">
                <small class="text-muted d-block fs-1">Kredensial Akses Default:</small>
                <span class="fw-bold text-dark fs-2">admin</span> / <span class="fw-bold text-dark fs-2">admin123</span>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>
</html>
