@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi Deteksi Dini - SMKN 1 Bungo')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-md-flex align-items-center justify-content-between mb-4">
          <div>
            <h5 class="card-title fw-semibold">Laporan &amp; Rekapitulasi Eksekutif Deteksi Dini</h5>
            <p class="card-subtitle mb-0">Cetak rekapitulasi data hasil deteksi cerdas untuk arsip resmi dan intervensi sekolah</p>
          </div>
          <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('laporan.pdf') }}" class="btn btn-danger d-flex align-items-center" target="_blank">
              <i class="ti ti-file-text me-1 fs-5"></i> Download PDF (Semua Siswa)
            </a>
            <a href="{{ route('laporan.pdf', ['risiko' => 'Tinggi']) }}" class="btn btn-outline-danger d-flex align-items-center" target="_blank">
              <i class="ti ti-alert-triangle me-1 fs-5"></i> Download PDF (Hanya Berisiko Tinggi)
            </a>
          </div>
        </div>

        <!-- Ringkasan Statistik Laporan -->
        <div class="row g-3 mb-4">
          <div class="col-md-3">
            <div class="p-3 border rounded bg-light text-center">
              <small class="text-muted d-block">Model Inferensi Digunakan</small>
              <strong class="fs-4 text-primary">{{ str_replace('_', ' ', $activeModel->algoritma ?? 'Random Forest') }}</strong>
            </div>
          </div>
          <div class="col-md-3">
            <div class="p-3 border rounded bg-light text-center">
              <small class="text-muted d-block">Akurasi Model</small>
              <strong class="fs-4 text-success">{{ $activeModel->akurasi ?? 0 }}%</strong>
            </div>
          </div>
          <div class="col-md-3">
            <div class="p-3 border rounded bg-light text-center">
              <small class="text-muted d-block">Total Siswa Terdeteksi Berisiko</small>
              <strong class="fs-4 text-danger">{{ $tinggiCount }} Siswa</strong>
            </div>
          </div>
          <div class="col-md-3">
            <div class="p-3 border rounded bg-light text-center">
              <small class="text-muted d-block">Total Siswa Kategori Aman</small>
              <strong class="fs-4 text-success">{{ $rendahCount }} Siswa</strong>
            </div>
          </div>
        </div>

        <!-- Preview Tabel Laporan -->
        <h6 class="fw-bold mb-3">Tinjauan Data Laporan Resmi:</h6>
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>No</th>
                <th>NISN</th>
                <th>Nama Peserta Didik</th>
                <th>Kelas</th>
                <th>Status Bantuan</th>
                <th>Jarak KM</th>
                <th>Status Deteksi</th>
                <th>Probabilitas</th>
                <th>Rangkuman Faktor Risiko</th>
              </tr>
            </thead>
            <tbody>
              @foreach($siswas as $idx => $s)
                <tr>
                  <td>{{ $idx + 1 }}</td>
                  <td>{{ $s->nisn }}</td>
                  <td class="fw-bold">{{ $s->nama_lengkap }}</td>
                  <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
                  <td>
                    @if($s->is_penerima_kip) KIP @endif
                    @if($s->is_layak_pip) PIP @endif
                    @if(!$s->is_penerima_kip && !$s->is_layak_pip) Reguler @endif
                  </td>
                  <td>{{ $s->jarak_rumah_km }} KM</td>
                  <td>
                    @if($s->latestPrediksi && $s->latestPrediksi->hasil_prediksi === 'Tinggi')
                      <span class="badge bg-danger">Tinggi</span>
                    @else
                      <span class="badge bg-success">Rendah</span>
                    @endif
                  </td>
                  <td>{{ $s->latestPrediksi ? number_format($s->latestPrediksi->probabilitas_risiko * 100, 1) . '%' : '-' }}</td>
                  <td><small class="text-muted">{{ $s->latestPrediksi->faktor_kunci ?? '-' }}</small></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
