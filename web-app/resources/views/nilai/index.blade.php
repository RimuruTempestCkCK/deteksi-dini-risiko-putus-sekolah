@extends('layouts.app')

@section('title', 'Leger Nilai & Presensi Rapor - SMKN 1 Bungo')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-md-flex align-items-center justify-content-between mb-4">
          <div>
            <h5 class="card-title fw-semibold">Leger Nilai Akademik &amp; Presensi Siswa</h5>
            <p class="card-subtitle mb-0">Rerata nilai mata pelajaran dan rekapitulasi kehadiran (Sakit, Izin, Alfa)</p>
          </div>
          <div class="mt-3 mt-md-0">
            <button type="button" class="btn btn-success d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalImportLeger">
              <i class="ti ti-file-upload me-1 fs-5"></i> Import File Leger Rapor Excel
            </button>
          </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('nilai.index') }}" class="row g-3 mb-4 p-3 bg-light rounded">
          <div class="col-md-4">
            <select name="semester" class="form-select">
              <option value="">-- Semua Semester --</option>
              <option value="Ganjil" {{ request('semester') == 'Ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
              <option value="Genap" {{ request('semester') == 'Genap' ? 'selected' : '' }}>Semester Genap</option>
            </select>
          </div>
          <div class="col-md-4">
            <select name="kelas_id" class="form-select">
              <option value="">-- Semua Kelas --</option>
              @foreach($kelasList as $k)
                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-secondary w-50">Filter</button>
            <a href="{{ route('nilai.index') }}" class="btn btn-outline-secondary w-50">Reset</a>
          </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>No</th>
                <th>NISN</th>
                <th>Nama Peserta Didik</th>
                <th>Kelas</th>
                <th>Tahun / Smt</th>
                <th>Rerata Nilai</th>
                <th>Mapel &lt; KKM</th>
                <th>Sakit (S)</th>
                <th>Izin (I)</th>
                <th>Alfa (A)</th>
              </tr>
            </thead>
            <tbody>
              @forelse($rapors as $idx => $r)
                <tr>
                  <td>{{ $rapors->firstItem() + $idx }}</td>
                  <td><span class="fw-semibold">{{ $r->siswa->nisn ?? '-' }}</span></td>
                  <td>
                    <h6 class="fw-bold mb-0">{{ $r->siswa->nama_lengkap ?? '-' }}</h6>
                  </td>
                  <td>{{ $r->siswa->kelas->nama_kelas ?? '-' }}</td>
                  <td>{{ $r->tahun_ajaran }} ({{ $r->semester }})</td>
                  <td>
                    <span class="badge {{ $r->rerata_nilai < 75 ? 'bg-danger' : 'bg-success' }} fs-3">
                      {{ $r->rerata_nilai }}
                    </span>
                  </td>
                  <td>
                    <span class="badge {{ $r->jml_mapel_dibawah_kkm > 2 ? 'bg-danger' : 'bg-light text-dark' }}">
                      {{ $r->jml_mapel_dibawah_kkm }} mapel
                    </span>
                  </td>
                  <td>{{ $r->total_sakit }} hari</td>
                  <td>{{ $r->total_izin }} hari</td>
                  <td>
                    <span class="badge {{ $r->total_alfa >= 10 ? 'bg-danger fs-3' : ($r->total_alfa >= 5 ? 'bg-warning text-dark' : 'bg-light text-dark') }}">
                      {{ $r->total_alfa }} hari
                    </span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="10" class="text-center py-4 text-muted">Belum ada data nilai atau leger rapor yang tercatat.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-4 d-flex justify-content-center">
          {{ $rapors->links() }}
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Import Leger -->
<div class="modal fade" id="modalImportLeger" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('nilai.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title fw-semibold">Import Berkas Leger Nilai &amp; Presensi Excel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted fs-3">Pilih berkas format Leger Rapor SMKN 1 Bungo (contoh: <code>Leger-Nilai-Akhir-Kelas-X-AK..xlsx</code>). Nilai mata pelajaran serta kolom kehadiran (S, I, A) akan diekstraksi otomatis.</p>
          <div class="mb-3">
            <label class="form-label fw-bold">Tahun Pelajaran</label>
            <input type="text" name="tahun_ajaran" class="form-control" value="2023/2024" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Semester</label>
            <select name="semester" class="form-select" required>
              <option value="Ganjil">Semester Ganjil</option>
              <option value="Genap">Semester Genap</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Pilih Berkas Excel Leger (.xlsx)</label>
            <input type="file" name="file_leger" class="form-control" accept=".xlsx, .xls" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">Mulai Proses Leger</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
