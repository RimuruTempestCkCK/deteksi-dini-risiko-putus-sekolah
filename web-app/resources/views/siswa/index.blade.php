@extends('layouts.app')

@section('title', 'Data Peserta Didik (Dapodik) - SMKN 1 Bungo')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-md-flex align-items-center justify-content-between mb-4">
          <div>
            <h5 class="card-title fw-semibold">Master Data Peserta Didik</h5>
            <p class="card-subtitle mb-0">Kelola informasi demografi, sosio-ekonomi (KIP, KPS, PIP), dan profil siswa</p>
          </div>
          <div class="mt-3 mt-md-0 d-flex gap-2">
            <!-- Modal Import Trigger -->
            <button type="button" class="btn btn-success d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalImportDapodik">
              <i class="ti ti-file-spreadsheet me-1 fs-5"></i> Import Excel Dapodik
            </button>
            <a href="{{ route('siswa.create') }}" class="btn btn-primary d-flex align-items-center">
              <i class="ti ti-plus me-1 fs-5"></i> Tambah Siswa
            </a>
          </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('siswa.index') }}" class="row g-3 mb-4 p-3 bg-light rounded">
          <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Cari Nama / NISN / NIPD..." value="{{ request('search') }}">
          </div>
          <div class="col-md-3">
            <select name="kelas_id" class="form-select">
              <option value="">-- Semua Kelas --</option>
              @foreach($kelasList as $k)
                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <select name="status_risiko" class="form-select">
              <option value="">-- Semua Status Risiko --</option>
              <option value="Tinggi" {{ request('status_risiko') == 'Tinggi' ? 'selected' : '' }}>Risiko Tinggi</option>
              <option value="Rendah" {{ request('status_risiko') == 'Rendah' ? 'selected' : '' }}>Aman (Rendah)</option>
            </select>
          </div>
          <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-secondary w-50">Filter</button>
            <a href="{{ route('siswa.index') }}" class="btn btn-outline-secondary w-50">Reset</a>
          </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>No</th>
                <th>NISN / NIPD</th>
                <th>Nama Peserta Didik</th>
                <th>Kelas</th>
                <th>Sosial-Ekonomi</th>
                <th>Jarak (KM)</th>
                <th>Status Risiko</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($siswas as $idx => $s)
                <tr>
                  <td>{{ $siswas->firstItem() + $idx }}</td>
                  <td>
                    <span class="fw-semibold">{{ $s->nisn }}</span><br>
                    <small class="text-muted">{{ $s->nipd ?? '-' }}</small>
                  </td>
                  <td>
                    <h6 class="fw-bold mb-0">{{ $s->nama_lengkap }}</h6>
                    <small class="text-muted">JK: {{ $s->jenis_kelamin }} | Anak ke: {{ $s->jml_saudara_kandung }} bersaudara</small>
                  </td>
                  <td>
                    <span class="badge bg-primary-subtle text-primary">{{ $s->kelas->nama_kelas ?? '-' }}</span>
                  </td>
                  <td>
                    @if($s->is_penerima_kip)
                      <span class="badge bg-success-subtle text-success me-1">KIP</span>
                    @endif
                    @if($s->is_penerima_kps)
                      <span class="badge bg-info-subtle text-info me-1">KPS</span>
                    @endif
                    @if($s->is_layak_pip)
                      <span class="badge bg-warning-subtle text-warning me-1">Usulan PIP</span>
                    @endif
                    @if(!$s->is_penerima_kip && !$s->is_penerima_kps && !$s->is_layak_pip)
                      <span class="badge bg-light text-muted">Reguler</span>
                    @endif
                  </td>
                  <td>{{ $s->jarak_rumah_km }} KM</td>
                  <td>
                    @if($s->latestPrediksi)
                      @if($s->latestPrediksi->hasil_prediksi === 'Tinggi')
                        <span class="badge bg-danger">Tinggi ({{ round($s->latestPrediksi->probabilitas_risiko * 100) }}%)</span>
                      @else
                        <span class="badge bg-success">Rendah</span>
                      @endif
                    @else
                      <span class="badge bg-secondary-subtle text-secondary">Belum Terdeteksi</span>
                    @endif
                  </td>
                  <td>
                    <div class="d-flex gap-1">
                      <a href="{{ route('siswa.show', $s->id) }}" class="btn btn-sm btn-info" title="Detail"><i class="ti ti-eye"></i></a>
                      <a href="{{ route('siswa.edit', $s->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="ti ti-edit"></i></a>
                      <form action="{{ route('siswa.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="ti ti-trash"></i></button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center py-4 text-muted">Tidak ada data siswa ditemukan.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-4">
          {{ $siswas->links() }}
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Import Dapodik -->
<div class="modal fade" id="modalImportDapodik" tabindex="-1" aria-labelledby="modalImportDapodikLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title fw-semibold" id="modalImportDapodikLabel">Import Data Siswa dari Dapodik</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted fs-3">Pilih berkas Excel (.xlsx / .xls) unduhan Dapodik (misal: <code>Daftar PD 23-24.xlsx</code>). Sistem akan membaca kolom NISN, Nama, Rombel, status KIP/KPS/PIP, dan Jarak Rumah secara otomatis.</p>
          <div class="mb-3">
            <label for="file_excel" class="form-label fw-bold">Pilih File Excel:</label>
            <input class="form-control" type="file" id="file_excel" name="file_excel" accept=".xlsx, .xls" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Mulai Import</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
