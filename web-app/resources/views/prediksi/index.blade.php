@extends('layouts.app')

@section('title', 'Hasil Deteksi Dini Risiko Putus Sekolah - SMKN 1 Bungo')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-md-flex align-items-center justify-content-between mb-4">
          <div>
            <h5 class="card-title fw-semibold">Hasil Deteksi Dini Risiko Putus Sekolah</h5>
            <p class="card-subtitle mb-0">Model Inferensi Aktif: <strong>{{ str_replace('_', ' ', $activeModel->algoritma ?? 'Random Forest') }}</strong> (Akurasi: {{ $activeModel->akurasi ?? 0 }}%)</p>
          </div>
          <div class="mt-3 mt-md-0 d-flex gap-2">
            <form action="{{ route('prediksi.batch') }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-primary d-flex align-items-center">
                <i class="ti ti-scan me-1"></i> Jalankan Deteksi Batch Seluruh Siswa
              </button>
            </form>
            <a href="{{ route('prediksi.simulasi') }}" class="btn btn-outline-primary d-flex align-items-center">
              <i class="ti ti-calculator me-1"></i> Simulasi Input Manual
            </a>
          </div>
        </div>

        <!-- Filter -->
        <form method="GET" action="{{ route('prediksi.index') }}" class="row g-3 mb-4 p-3 bg-light rounded">
          <div class="col-md-4">
            <select name="hasil_prediksi" class="form-select">
              <option value="">-- Semua Hasil Prediksi --</option>
              <option value="Tinggi" {{ request('hasil_prediksi') == 'Tinggi' ? 'selected' : '' }}>Risiko Tinggi</option>
              <option value="Rendah" {{ request('hasil_prediksi') == 'Rendah' ? 'selected' : '' }}>Aman (Rendah)</option>
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
            <a href="{{ route('prediksi.index') }}" class="btn btn-outline-secondary w-50">Reset</a>
          </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>No</th>
                <th>Nama Peserta Didik</th>
                <th>Kelas</th>
                <th>Hasil Deteksi</th>
                <th>Probabilitas</th>
                <th>Faktor Kunci Risiko</th>
                <th>Tanggal Prediksi</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($prediksis as $idx => $p)
                <tr>
                  <td>{{ $prediksis->firstItem() + $idx }}</td>
                  <td>
                    <h6 class="fw-bold mb-0">{{ $p->siswa->nama_lengkap ?? '-' }}</h6>
                    <small class="text-muted">NISN: {{ $p->siswa->nisn ?? '-' }}</small>
                  </td>
                  <td>{{ $p->siswa->kelas->nama_kelas ?? '-' }}</td>
                  <td>
                    @if($p->hasil_prediksi === 'Tinggi')
                      <span class="badge bg-danger fs-3"><i class="ti ti-alert-triangle me-1"></i> Risiko Tinggi</span>
                    @else
                      <span class="badge bg-success fs-3"><i class="ti ti-circle-check me-1"></i> Aman (Rendah)</span>
                    @endif
                  </td>
                  <td>
                    <strong>{{ number_format($p->probabilitas_risiko * 100, 1) }}%</strong>
                  </td>
                  <td>
                    <small class="text-muted">{{ $p->faktor_kunci }}</small>
                  </td>
                  <td>{{ $p->tanggal_prediksi->format('d/m/Y') }}</td>
                  <td>
                    <!-- Button Modal Detail Prediksi -->
                    <button type="button" class="btn btn-sm btn-info text-white p-1" data-bs-toggle="modal" data-bs-target="#modalDetailPrediksi{{ $p->id }}" title="Detail Prediksi">
                      <iconify-icon icon="solar:eye-linear"></iconify-icon>
                    </button>
                  </td>
                </tr>

                <!-- MODAL DETAIL PREDIKSI -->
                <div class="modal fade" id="modalDetailPrediksi{{ $p->id }}" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                      <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-dark">
                          <iconify-icon icon="solar:shield-warning-linear" class="me-1 fs-5 text-primary"></iconify-icon> Rincian Deteksi: {{ $p->siswa->nama_lengkap ?? '-' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body p-4">
                        <div class="text-center p-3 rounded mb-3 {{ $p->hasil_prediksi == 'Tinggi' ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                          <span class="fs-2 text-uppercase fw-semibold d-block">Klasifikasi Sistem Cerdas</span>
                          <h4 class="fw-bolder mb-1 {{ $p->hasil_prediksi == 'Tinggi' ? 'text-danger' : 'text-success' }}">
                            RISIKO {{ strtoupper($p->hasil_prediksi) }}
                          </h4>
                          <span class="badge {{ $p->hasil_prediksi == 'Tinggi' ? 'bg-danger' : 'bg-success' }} fs-3">
                            Probabilitas: {{ number_format($p->probabilitas_risiko * 100, 1) }}%
                          </span>
                        </div>
                        <ul class="list-group list-group-flush border rounded mb-3">
                          <li class="list-group-item d-flex justify-content-between"><span>NISN:</span><strong>{{ $p->siswa->nisn ?? '-' }}</strong></li>
                          <li class="list-group-item d-flex justify-content-between"><span>Kelas:</span><strong>{{ $p->siswa->kelas->nama_kelas ?? '-' }}</strong></li>
                          <li class="list-group-item d-flex justify-content-between"><span>Model Evaluasi:</span><strong>{{ str_replace('_', ' ', $p->modelEvaluasi->algoritma ?? 'Random Forest') }}</strong></li>
                          <li class="list-group-item d-flex justify-content-between"><span>Tanggal Deteksi:</span><strong>{{ $p->tanggal_prediksi->format('d F Y') }}</strong></li>
                        </ul>
                        <div>
                          <strong class="d-block mb-1 text-dark">Faktor Pemicu Risiko:</strong>
                          <div class="p-3 bg-light rounded text-muted fs-3">
                            {{ $p->faktor_kunci }}
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                      </div>
                    </div>
                  </div>
                </div>
              @empty
                <tr>
                  <td colspan="8" class="text-center py-4 text-muted">Belum ada data log prediksi. Silakan jalankan deteksi batch.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-4 d-flex justify-content-center">
          {{ $prediksis->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
