@extends('layouts.app')

@section('title', 'Pipeline KDD & Data Mining - SMKN 1 Bungo')

@section('content')
<div class="row">
  <div class="col-12 mb-4">
    <div class="card bg-info-subtle text-info border-0 shadow-none">
      <div class="card-body p-4">
        <h4 class="fw-bold mb-1 text-info"><i class="ti ti-database me-2"></i>Knowledge Discovery in Databases (KDD) Pipeline</h4>
        <p class="mb-0 text-dark">Mengolah data mentah siswa dan nilai menjadi fitur terstruktur untuk melatih algoritma <strong>Random Forest</strong> dan <strong>Naïve Bayes</strong>.</p>
      </div>
    </div>
  </div>

  <!-- Statistik Dataset -->
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <span class="text-muted fs-3">Total Sampel Data Fitur</span>
        <h2 class="fw-bold mb-0 text-primary">{{ number_format($totalFitur) }}</h2>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <span class="text-muted fs-3">Label Aktual: Risiko Tinggi</span>
        <h2 class="fw-bold mb-0 text-danger">{{ number_format($tinggiCount) }}</h2>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <span class="text-muted fs-3">Label Aktual: Aman (Rendah)</span>
        <h2 class="fw-bold mb-0 text-success">{{ number_format($rendahCount) }}</h2>
      </div>
    </div>
  </div>

  <!-- Panel Training Controller -->
  <div class="col-12 mb-4">
    <div class="card border-primary border">
      <div class="card-body">
        <div class="d-md-flex align-items-center justify-content-between">
          <div>
            <h5 class="card-title fw-bold text-primary mb-1">
              <i class="ti ti-player-play me-1"></i> Eksekusi Pelatihan &amp; Pengujian Model (Mining Pipeline)
            </h5>
            <p class="card-subtitle mb-0">Latih model Random Forest dan Naïve Bayes secara simultan berdasarkan data fitur terkini.</p>
          </div>
          <form action="{{ route('mining.train') }}" method="POST" class="mt-3 mt-md-0 d-flex align-items-center gap-3">
            @csrf
            <div>
              <label class="form-label mb-0 small text-muted">Rasio Split Data Latih : Uji</label>
              <select name="split_ratio" class="form-select form-select-sm">
                <option value="0.8" selected>80% Latih : 20% Uji (Standar)</option>
                <option value="0.7">70% Latih : 30% Uji</option>
                <option value="0.9">90% Latih : 10% Uji</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary d-flex align-items-center mt-3">
              <i class="ti ti-refresh me-1"></i> Mulai Training Model
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Preview Vektor Fitur KDD -->
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">Tabel Fitur Hasil Preprocessing (Dataset KDD)</h5>
        <div class="table-responsive">
          <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>No</th>
                <th>Nama Peserta Didik</th>
                <th>Rerata Nilai (X1)</th>
                <th>Mapel &lt; KKM (X2)</th>
                <th>Jml Alfa (X3)</th>
                <th>Izin + Sakit (X4)</th>
                <th>Status Ekonomi (X5)</th>
                <th>Jarak KM (X6)</th>
                <th>Tanggungan (X7)</th>
                <th>Target Aktual (Y)</th>
              </tr>
            </thead>
            <tbody>
              @forelse($datasets as $idx => $d)
                <tr>
                  <td>{{ $datasets->firstItem() + $idx }}</td>
                  <td class="fw-semibold">{{ $d->siswa->nama_lengkap ?? '-' }}</td>
                  <td>{{ $d->rerata_nilai }}</td>
                  <td>{{ $d->mapel_dibawah_kkm }}</td>
                  <td><span class="badge {{ $d->jml_alfa >= 10 ? 'bg-danger' : 'bg-light text-dark' }}">{{ $d->jml_alfa }} hari</span></td>
                  <td>{{ $d->jml_izin_sakit }} hari</td>
                  <td>
                    @if($d->kode_ekonomi == 1)
                      <span class="badge bg-danger-subtle text-danger">1 (Rentan/Bansos)</span>
                    @elseif($d->kode_ekonomi == 2)
                      <span class="badge bg-warning-subtle text-warning">2 (Menengah)</span>
                    @else
                      <span class="badge bg-success-subtle text-success">3 (Mampu)</span>
                    @endif
                  </td>
                  <td>{{ $d->jarak_km }} KM</td>
                  <td>{{ $d->jml_saudara }} anak</td>
                  <td>
                    <span class="badge {{ $d->label_aktual == 'Tinggi' ? 'bg-danger' : 'bg-success' }}">
                      {{ $d->label_aktual }}
                    </span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="10" class="text-center py-4 text-muted">Belum ada fitur dataset KDD yang diekstraksi.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-4">
          {{ $datasets->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
