@extends('layouts.app')

@section('title', 'Simulasi Manual Prediksi Risiko Siswa - SMKN 1 Bungo')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8 col-xl-7">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <div class="d-flex align-items-center mb-3">
          <div class="bg-primary-subtle text-primary rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <iconify-icon icon="solar:calculator-minimalistic-linear" class="fs-6"></iconify-icon>
          </div>
          <div>
            <h5 class="card-title fw-bold mb-0">Simulasi Deteksi Dini Risiko Siswa</h5>
            <small class="text-muted">Komparasi Inferensi <strong>Random Forest (100 Trees)</strong> vs <strong>Naïve Bayes (Gaussian)</strong> (8 Fitur Prediktor)</small>
          </div>
        </div>

        <form action="{{ route('prediksi.simulasi.process') }}" method="POST">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">X1: Rerata Nilai Rapor</label>
              <input type="number" step="0.01" name="X1_Nilai" class="form-control" value="{{ old('X1_Nilai', 79.49) }}" min="0" max="100" required>
              <small class="text-muted">Skala 0.00 - 100.00 (Contoh D1: 79.49)</small>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">X2: Jumlah Mapel &lt; KKM</label>
              <input type="number" name="X2_KKM" class="form-control" value="{{ old('X2_KKM', 1) }}" min="0" required>
              <small class="text-muted">Jumlah mapel belum tuntas (Contoh D1: 1)</small>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">X3: Ketidakhadiran Alfa (Hari)</label>
              <input type="number" name="X3_Alfa" class="form-control" value="{{ old('X3_Alfa', 7) }}" min="0" required>
              <small class="text-muted">Presensi tanpa keterangan (Contoh D1: 7)</small>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">X4: Ketidakhadiran Izin &amp; Sakit</label>
              <input type="number" name="X4_IzinSakit" class="form-control" value="{{ old('X4_IzinSakit', 4) }}" min="0" required>
              <small class="text-muted">Total hari sakit dan izin (Contoh D1: 4)</small>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">X5: Status Ekonomi Keluarga</label>
              <select name="X5_Ekonomi" class="form-select" required>
                <option value="1" {{ old('X5_Ekonomi') == 1 ? 'selected' : '' }}>Kode 1 - Bansos (Penerima KPS / KIP / PIP)</option>
                <option value="2" {{ old('X5_Ekonomi', 2) == 2 ? 'selected' : '' }}>Kode 2 - Rentan Finansial (&lt; Rp 1 Juta)</option>
                <option value="3" {{ old('X5_Ekonomi') == 3 ? 'selected' : '' }}>Kode 3 - Mampu / Mandiri (&gt;= Rp 1 Juta)</option>
              </select>
              <small class="text-muted">Kategori sosio-ekonomi keluarga</small>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">X6: Jarak Rumah ke Sekolah (KM)</label>
              <input type="number" step="0.1" name="X6_Jarak" class="form-control" value="{{ old('X6_Jarak', 15.0) }}" min="0" required>
              <small class="text-muted">Jarak geografis (Contoh D1: 15.0 KM)</small>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">X7: Pelanggaran Disiplin Siswa</label>
              <input type="number" name="X7_Pelanggaran" class="form-control" value="{{ old('X7_Pelanggaran', 1) }}" min="0" required>
              <small class="text-muted">Catatan kasus tata tertib (Contoh D1: 1)</small>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">X8: Skor Dukungan &amp; Ekskul</label>
              <select name="X8_Dukungan" class="form-select" required>
                <option value="1" {{ old('X8_Dukungan') == 1 ? 'selected' : '' }}>Skor 1 - Stabil (Dukungan Baik &amp; Ekskul Aktif)</option>
                <option value="2" {{ old('X8_Dukungan') == 2 ? 'selected' : '' }}>Skor 2 - Perhatian (Salah Satu Kurang/Pasif)</option>
                <option value="3" {{ old('X8_Dukungan', 3) == 3 ? 'selected' : '' }}>Skor 3 - Rentan Tinggi (Kurang &amp; Tidak Aktif)</option>
              </select>
              <small class="text-muted">Interaksi dukungan wali &amp; kegiatan sekolah</small>
            </div>
          </div>

          <div class="mt-4 pt-2">
            <button type="submit" class="btn btn-primary w-100 py-2 fs-4 fw-semibold d-flex align-items-center justify-content-center">
              <iconify-icon icon="solar:round-alt-arrow-right-linear" class="me-2 fs-5"></iconify-icon> Jalankan Simulasi Prediksi (RF &amp; NB)
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- MODAL POP-UP HASIL ANALISIS SIMULASI -->
@if(session('simulasi_result'))
  @php $res = session('simulasi_result'); @endphp
  <div class="modal fade" id="modalHasilSimulasi" tabindex="-1" aria-labelledby="modalHasilSimulasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header {{ $res['label'] == 'Tinggi' ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
          <h5 class="modal-title fw-bold" id="modalHasilSimulasiLabel">
            <iconify-icon icon="solar:chart-square-linear" class="me-2 fs-5"></iconify-icon> Hasil Analisis Simulasi Siswa
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <!-- Card Komparasi Random Forest vs Naïve Bayes -->
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <div class="p-3 rounded border {{ $res['rf_label'] == 'Tinggi' ? 'border-danger bg-danger-subtle' : 'border-success bg-success-subtle' }}">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="fw-bold">Random Forest (Ensemble):</span>
                  <span class="badge {{ $res['rf_label'] == 'Tinggi' ? 'bg-danger' : 'bg-success' }}">
                    RISIKO {{ strtoupper($res['rf_label']) }}
                  </span>
                </div>
                <div class="small">
                  Probabilitas Risiko: <strong>{{ number_format($res['rf_prob'] * 100, 2) }}%</strong>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="p-3 rounded border {{ $res['nb_label'] == 'Tinggi' ? 'border-danger bg-danger-subtle' : 'border-success bg-success-subtle' }}">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="fw-bold">Naïve Bayes (Gaussian):</span>
                  <span class="badge {{ $res['nb_label'] == 'Tinggi' ? 'bg-danger' : 'bg-success' }}">
                    RISIKO {{ strtoupper($res['nb_label']) }}
                  </span>
                </div>
                <div class="small">
                  Probabilitas Risiko: <strong>{{ number_format($res['nb_prob'] * 100, 2) }}%</strong>
                </div>
              </div>
            </div>
          </div>

          <!-- Faktor Kunci & Rekomendasi -->
          <div class="mb-3">
            <h6 class="fw-bold text-dark"><iconify-icon icon="solar:danger-triangle-linear" class="text-warning me-1"></iconify-icon> Faktor Pemicu Utama:</h6>
            <div class="p-3 bg-light rounded text-muted">
              {{ $res['faktor_kunci'] }}
            </div>
          </div>

          <div class="mb-2">
            <h6 class="fw-bold text-dark"><iconify-icon icon="solar:shield-warning-linear" class="text-primary me-1"></iconify-icon> Rekomendasi Tindakan Intervensi:</h6>
            <div class="p-3 rounded {{ ($res['rf_label'] == 'Tinggi' || $res['nb_label'] == 'Tinggi') ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
              @if($res['rf_label'] == 'Tinggi' || $res['nb_label'] == 'Tinggi')
                <strong>Peringatan Dini!</strong> Siswa terindikasi memiliki risiko tinggi putus sekolah. Segera jadwalkan konseling BK, monitoring presensi harian, dan koordinasi home-visit dengan orang tua siswa.
              @else
                <strong>Kondisi Aman!</strong> Profil akademik dan kedisiplinan siswa stabil. Tetap pertahankan monitoring berkala rutin tiap semester.
              @endif
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>
@endif
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    @if(session('simulasi_result'))
      var myModal = new bootstrap.Modal(document.getElementById('modalHasilSimulasi'));
      myModal.show();
    @endif
  });
</script>
@endpush
