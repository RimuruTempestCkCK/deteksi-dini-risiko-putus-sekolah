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
            <h5 class="card-title fw-bold mb-0">Kalkulator Simulasi Risiko Siswa</h5>
            <small class="text-muted">Uji inferensi model cerdas <strong>{{ str_replace('_', ' ', $activeModel->algoritma ?? 'Random Forest') }}</strong> secara real-time</small>
          </div>
        </div>

        <form action="{{ route('prediksi.simulasi.process') }}" method="POST">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">1. Rerata Nilai Rapor Semester</label>
              <input type="number" step="0.1" name="rerata_nilai" class="form-control" value="{{ old('rerata_nilai', 72.5) }}" min="0" max="100" required>
              <small class="text-muted">Skala 0 - 100</small>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">2. Mapel di Bawah KKM</label>
              <input type="number" name="mapel_dibawah_kkm" class="form-control" value="{{ old('mapel_dibawah_kkm', 3) }}" min="0" required>
              <small class="text-muted">Jumlah mata pelajaran</small>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">3. Ketidakhadiran Alfa</label>
              <input type="number" name="jml_alfa" class="form-control" value="{{ old('jml_alfa', 12) }}" min="0" required>
              <small class="text-muted">Hari tanpa keterangan</small>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">4. Izin + Sakit</label>
              <input type="number" name="jml_izin_sakit" class="form-control" value="{{ old('jml_izin_sakit', 6) }}" min="0" required>
              <small class="text-muted">Total hari izin &amp; sakit</small>
            </div>
            <div class="col-12">
              <label class="form-label fw-bold">5. Tingkat Kondisi Finansial / Ekonomi</label>
              <select name="kode_ekonomi" class="form-select" required>
                <option value="1">Kode 1 - Rentan Finansial / Penerima KIP / Layak PIP</option>
                <option value="2">Kode 2 - Menengah (Penghasilan 2 - 4 Juta)</option>
                <option value="3">Kode 3 - Mampu / Cukup</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">6. Jarak Rumah ke Sekolah (KM)</label>
              <input type="number" step="0.1" name="jarak_km" class="form-control" value="{{ old('jarak_km', 16.5) }}" min="0" required>
              <small class="text-muted">Dalam satuan kilometer</small>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">7. Tanggungan / Saudara Kandung</label>
              <input type="number" name="jml_saudara" class="form-control" value="{{ old('jml_saudara', 4) }}" min="0" required>
              <small class="text-muted">Jumlah anak / saudara</small>
            </div>
          </div>

          <div class="mt-4 pt-2">
            <button type="submit" class="btn btn-primary w-100 py-2 fs-4 fw-semibold d-flex align-items-center justify-content-center">
              <iconify-icon icon="solar:round-alt-arrow-right-linear" class="me-2 fs-5"></iconify-icon> Jalankan Simulasi Prediksi
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
            <iconify-icon icon="solar:chart-square-linear" class="me-2 fs-5"></iconify-icon> Hasil Analisis Simulasi
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <!-- Card Status Risiko Utama -->
          <div class="alert {{ $res['label'] == 'Tinggi' ? 'alert-danger border-danger' : 'alert-success border-success' }} p-3 mb-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="fs-4 fw-bold">Tingkat Risiko:</span>
              <span class="badge {{ $res['label'] == 'Tinggi' ? 'bg-danger' : 'bg-success' }} fs-5 px-3 py-2">
                {{ $res['label'] }}
              </span>
            </div>
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="fs-3 text-muted">Probabilitas Terhitung:</span>
              <span class="fs-4 fw-bolder">{{ number_format($res['probabilitas'] * 100, 1) }}%</span>
            </div>
            <hr class="my-2">
            <div>
              <strong>Faktor Kunci yang Teridentifikasi:</strong>
              <p class="mb-0 mt-1 fs-3">{{ $res['faktor_kunci'] }}</p>
            </div>
          </div>

          <!-- Parameter Masukan yang Diuji -->
          <h6 class="fw-bold mb-2 text-dark">Parameter Masukan yang Diuji:</h6>
          <div class="row g-2 mb-3">
            <div class="col-sm-6">
              <div class="p-2 border rounded bg-light d-flex justify-content-between">
                <span class="text-muted">Rerata Nilai:</span>
                <strong>{{ $res['input']['rerata_nilai'] }}</strong>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="p-2 border rounded bg-light d-flex justify-content-between">
                <span class="text-muted">Mapel &lt; KKM:</span>
                <strong>{{ $res['input']['mapel_dibawah_kkm'] }}</strong>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="p-2 border rounded bg-light d-flex justify-content-between">
                <span class="text-muted">Ketidakhadiran Alfa:</span>
                <strong>{{ $res['input']['jml_alfa'] }} hari</strong>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="p-2 border rounded bg-light d-flex justify-content-between">
                <span class="text-muted">Izin + Sakit:</span>
                <strong>{{ $res['input']['jml_izin_sakit'] }} hari</strong>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="p-2 border rounded bg-light d-flex justify-content-between">
                <span class="text-muted">Status Ekonomi:</span>
                <strong>Kode {{ $res['input']['kode_ekonomi'] }}</strong>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="p-2 border rounded bg-light d-flex justify-content-between">
                <span class="text-muted">Jarak Rumah:</span>
                <strong>{{ $res['input']['jarak_km'] }} KM</strong>
              </div>
            </div>
            <div class="col-12">
              <div class="p-2 border rounded bg-light d-flex justify-content-between">
                <span class="text-muted">Jml Saudara:</span>
                <strong>{{ $res['input']['jml_saudara'] }} orang</strong>
              </div>
            </div>
          </div>

          <div class="text-end">
            <small class="text-muted">Dianalisis menggunakan Model: <strong>{{ str_replace('_', ' ', $res['model']) }}</strong></small>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      var modalHasil = new bootstrap.Modal(document.getElementById('modalHasilSimulasi'));
      modalHasil.show();
    });
  </script>
  @endpush
@endif
@endsection
