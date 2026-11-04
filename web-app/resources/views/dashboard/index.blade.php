@extends('layouts.app')

@section('title', 'Dashboard Early Warning System - SMKN 1 Bungo')

@section('content')
<div class="row">
  <!-- Header Banner -->
  <div class="col-12 mb-4">
    <div class="card bg-primary-subtle text-primary border-0 shadow-none">
      <div class="card-body p-4">
        <div class="d-md-flex align-items-center justify-content-between">
          <div>
            <h3 class="fw-bold mb-1 text-primary">Sistem Deteksi Dini Risiko Siswa Putus Sekolah</h3>
            <p class="mb-0 text-dark">Komparasi Metode <strong>Random Forest</strong> &amp; <strong>Naïve Bayes</strong> di SMK Negeri 1 Bungo</p>
          </div>
          <div class="mt-3 mt-md-0 d-flex gap-2">
            <form action="{{ route('prediksi.batch') }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-primary d-flex align-items-center">
                <i class="ti ti-scan me-1 fs-5"></i> Jalankan Deteksi Batch
              </button>
            </form>
            <a href="{{ route('komparasi.index') }}" class="btn btn-outline-primary d-flex align-items-center">
              <i class="ti ti-git-compare me-1 fs-5"></i> Cek Komparasi Model
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- KPI Cards -->
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <span class="fs-3 fw-semibold text-muted">Total Peserta Didik</span>
          <span class="badge bg-primary-subtle text-primary rounded-pill p-2"><i class="ti ti-users fs-6"></i></span>
        </div>
        <h2 class="fw-bolder mb-1">{{ number_format($totalSiswa) }}</h2>
        <span class="text-muted fs-2">Data Dapodik Terdaftar</span>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <span class="fs-3 fw-semibold text-danger">Risiko Putus Sekolah (Tinggi)</span>
          <span class="badge bg-danger-subtle text-danger rounded-pill p-2"><i class="ti ti-alert-triangle fs-6"></i></span>
        </div>
        <h2 class="fw-bolder mb-1 text-danger">{{ number_format($highRiskCount) }}</h2>
        <span class="text-danger fs-2 fw-medium">Butuh Intervensi Segera</span>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <span class="fs-3 fw-semibold text-success">Kategori Kondusif (Rendah)</span>
          <span class="badge bg-success-subtle text-success rounded-pill p-2"><i class="ti ti-circle-check fs-6"></i></span>
        </div>
        <h2 class="fw-bolder mb-1 text-success">{{ number_format($lowRiskCount) }}</h2>
        <span class="text-success fs-2 fw-medium">Kondisi Belajar Aman</span>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <span class="fs-3 fw-semibold text-muted">Model Inferensi Aktif</span>
          <span class="badge bg-warning-subtle text-warning rounded-pill p-2"><i class="ti ti-cpu fs-6"></i></span>
        </div>
        <h4 class="fw-bolder mb-1 text-truncate">{{ $activeModel ? str_replace('_', ' ', $activeModel->algoritma) : 'Belum Ada' }}</h4>
        <span class="text-muted fs-2">Akurasi: <strong>{{ $activeModel ? $activeModel->akurasi : 0 }}%</strong> | F1: <strong>{{ $activeModel ? $activeModel->f1_score : 0 }}%</strong></span>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Chart Risiko per Jurusan -->
  <div class="col-lg-8 d-flex align-items-strech">
    <div class="card w-100">
      <div class="card-body">
        <div class="d-sm-flex d-block align-items-center justify-content-between mb-9">
          <div class="mb-3 mb-sm-0">
            <h5 class="card-title fw-semibold">Distribusi Risiko Putus Sekolah per Kompetensi Keahlian (Jurusan)</h5>
            <p class="card-subtitle mb-0">Analisis sebaran siswa berisiko tinggi vs aman di SMK Negeri 1 Bungo</p>
          </div>
        </div>
        <div id="chart-jurusan" style="min-height: 320px;"></div>
      </div>
    </div>
  </div>

  <!-- Donut Chart & Rekap Intervensi -->
  <div class="col-lg-4 d-flex align-items-strech">
    <div class="card w-100">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">Rasio Klasifikasi Global</h5>
        <div id="chart-pie-risiko" class="d-flex justify-content-center"></div>
        <hr>
        <h6 class="fw-semibold mb-2 fs-3">Status Intervensi Siswa:</h6>
        <div class="d-flex justify-content-between py-2 border-bottom">
          <span class="text-muted">Pending:</span>
          <span class="badge bg-secondary-subtle text-secondary fw-bold">{{ $intervensiPending }}</span>
        </div>
        <div class="d-flex justify-content-between py-2 border-bottom">
          <span class="text-muted">Dalam Pemantauan:</span>
          <span class="badge bg-warning-subtle text-warning fw-bold">{{ $intervensiMonitoring }}</span>
        </div>
        <div class="d-flex justify-content-between py-2">
          <span class="text-muted">Selesai / Teratasi:</span>
          <span class="badge bg-success-subtle text-success fw-bold">{{ $intervensiSelesai }}</span>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Urgent Student Action List -->
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="card-title fw-semibold text-danger mb-1">
              <i class="ti ti-urgent me-1"></i> Prioritas Penanganan Siswa Berisiko Tinggi
            </h5>
            <p class="card-subtitle">5 siswa teratas dengan probabilitas risiko putus sekolah tertinggi</p>
          </div>
          <a href="{{ route('prediksi.index', ['hasil_prediksi' => 'Tinggi']) }}" class="btn btn-sm btn-outline-danger">
            Lihat Semua Siswa Berisiko
          </a>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Nama Peserta Didik</th>
                <th>Kelas / Jurusan</th>
                <th>Probabilitas Risiko</th>
                <th>Faktor Kunci Risiko</th>
                <th>Status Intervensi</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($urgentStudents as $s)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="me-3">
                        <span class="badge bg-danger-subtle text-danger rounded-circle p-2 fs-4">
                          <i class="ti ti-user-x"></i>
                        </span>
                      </div>
                      <div>
                        <h6 class="fw-bold mb-0">{{ $s->nama_lengkap }}</h6>
                        <small class="text-muted">NISN: {{ $s->nisn }}</small>
                      </div>
                    </div>
                  </td>
                  <td>{{ $s->kelas->nama_kelas ?? '-' }} ({{ $s->kelas->jurusan ?? '-' }})</td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="progress flex-grow-1" style="height: 8px;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ ($s->latestPrediksi->probabilitas_risiko ?? 0) * 100 }}%"></div>
                      </div>
                      <span class="badge bg-danger">{{ number_format(($s->latestPrediksi->probabilitas_risiko ?? 0) * 100, 1) }}%</span>
                    </div>
                  </td>
                  <td>
                    <small class="text-muted">{{ $s->latestPrediksi->faktor_kunci ?? '-' }}</small>
                  </td>
                  <td>
                    @php
                      $latestInter = $s->tindakanIntervensis->sortByDesc('id')->first();
                    @endphp
                    @if($latestInter)
                      <span class="badge bg-warning-subtle text-warning">{{ $latestInter->status_intervensi }}</span>
                    @else
                      <span class="badge bg-secondary-subtle text-secondary">Belum ada tindakan</span>
                    @endif
                  </td>
                  <td>
                    <a href="{{ route('siswa.show', $s->id) }}" class="btn btn-sm btn-primary">
                      <i class="ti ti-eye"></i> Detail
                    </a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4 text-muted">Belum ada siswa yang terdeteksi berisiko tinggi.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener("DOMContentLoaded", function () {
    // 1. Bar Chart Jurusan
    var jurusanCategories = {!! json_encode($jurusanStats->pluck('jurusan')) !!};
    var dataTinggi = {!! json_encode($jurusanStats->pluck('tinggi')) !!};
    var dataRendah = {!! json_encode($jurusanStats->pluck('rendah')) !!};

    var chartJurusanOptions = {
      series: [
        { name: 'Risiko Tinggi', data: dataTinggi },
        { name: 'Aman / Rendah', data: dataRendah }
      ],
      chart: {
        type: 'bar',
        height: 330,
        toolbar: { show: false }
      },
      colors: ['#fa896b', '#13deb9'],
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '45%',
          borderRadius: 4
        },
      },
      dataLabels: { enabled: false },
      stroke: { show: true, width: 2, colors: ['transparent'] },
      xaxis: { categories: jurusanCategories },
      yaxis: { title: { text: 'Jumlah Siswa' } },
      fill: { opacity: 1 },
      tooltip: {
        y: {
          formatter: function (val) {
            return val + " Siswa";
          }
        }
      }
    };
    var chartJurusan = new ApexCharts(document.querySelector("#chart-jurusan"), chartJurusanOptions);
    chartJurusan.render();

    // 2. Donut Chart Risiko Global
    var chartPieOptions = {
      series: [{{ $highRiskCount }}, {{ $lowRiskCount }}],
      labels: ['Risiko Tinggi', 'Aman (Rendah)'],
      chart: {
        type: 'donut',
        width: 320,
      },
      colors: ['#fa896b', '#13deb9'],
      dataLabels: { enabled: false },
      legend: { position: 'bottom' },
      responsive: [{
        breakpoint: 480,
        options: {
          chart: { width: 200 },
          legend: { position: 'bottom' }
        }
      }]
    };
    var chartPie = new ApexCharts(document.querySelector("#chart-pie-risiko"), chartPieOptions);
    chartPie.render();
  });
</script>
@endpush
