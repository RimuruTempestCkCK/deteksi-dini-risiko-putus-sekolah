@extends('layouts.app')

@section('title', 'Simulasi Manual Prediksi Risiko Siswa - SMKN 1 Bungo')

@section('content')
<div class="row">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-1">Kalkulator Simulasi Risiko Siswa</h5>
        <p class="card-subtitle mb-4">Input data uji untuk menguji respon model inferensi <strong>{{ str_replace('_', ' ', $activeModel->algoritma ?? 'Random Forest') }}</strong> secara real-time.</p>

        <form action="{{ route('prediksi.simulasi.process') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label class="form-label fw-bold">1. Rerata Nilai Rapor Semester (0 - 100)</label>
            <input type="number" step="0.1" name="rerata_nilai" class="form-control" value="{{ old('rerata_nilai', 72.5) }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">2. Jumlah Mapel di Bawah KKM</label>
            <input type="number" name="mapel_dibawah_kkm" class="form-control" value="{{ old('mapel_dibawah_kkm', 3) }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">3. Jumlah Ketidakhadiran Tanpa Keterangan (Alfa)</label>
            <input type="number" name="jml_alfa" class="form-control" value="{{ old('jml_alfa', 12) }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">4. Total Kehadiran Izin + Sakit</label>
            <input type="number" name="jml_izin_sakit" class="form-control" value="{{ old('jml_izin_sakit', 6) }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">5. Tingkat Kondisi Ekonomi Keluarga</label>
            <select name="kode_ekonomi" class="form-select" required>
              <option value="1">1 - Rentan Finansial / Penerima KIP / Layak PIP</option>
              <option value="2">2 - Menengah (Penghasilan 2-4 Juta)</option>
              <option value="3">3 - Mampu / Cukup</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">6. Jarak Rumah ke Sekolah (KM)</label>
            <input type="number" step="0.1" name="jarak_km" class="form-control" value="{{ old('jarak_km', 16.5) }}" required>
          </div>
          <div class="mb-4">
            <label class="form-label fw-bold">7. Jumlah Saudara Kandung / Tanggungan</label>
            <input type="number" name="jml_saudara" class="form-control" value="{{ old('jml_saudara', 4) }}" required>
          </div>

          <button type="submit" class="btn btn-primary w-100 py-2 fs-4">
            <i class="ti ti-scan me-1"></i> Jalankan Simulasi Prediksi
          </button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">Hasil Analisis Simulasi</h5>

        @if(session('simulasi_result'))
          @php $res = session('simulasi_result'); @endphp
          <div class="alert {{ $res['label'] == 'Tinggi' ? 'alert-danger border-danger' : 'alert-success border-success' }} p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="fs-4 fw-bold">Tingkat Risiko:</span>
              <span class="badge {{ $res['label'] == 'Tinggi' ? 'bg-danger' : 'bg-success' }} fs-5 px-3 py-2">
                {{ $res['label'] }}
              </span>
            </div>
            <div class="d-flex align-items-center justify-content-between mb-3">
              <span class="fs-3 text-muted">Probabilitas Terhitung:</span>
              <span class="fs-4 fw-bolder">{{ number_format($res['probabilitas'] * 100, 1) }}%</span>
            </div>
            <hr>
            <div>
              <strong>Faktor Kunci yang Teridentifikasi:</strong>
              <p class="mb-0 mt-1">{{ $res['faktor_kunci'] }}</p>
            </div>
          </div>

          <h6 class="fw-bold mb-2">Parameter Masukan yang Diuji:</h6>
          <ul class="list-group list-group-flush mb-3">
            <li class="list-group-item d-flex justify-content-between"><span>Rerata Nilai:</span><strong>{{ $res['input']['rerata_nilai'] }}</strong></li>
            <li class="list-group-item d-flex justify-content-between"><span>Mapel &lt; KKM:</span><strong>{{ $res['input']['mapel_dibawah_kkm'] }}</strong></li>
            <li class="list-group-item d-flex justify-content-between"><span>Ketidakhadiran Alfa:</span><strong>{{ $res['input']['jml_alfa'] }} hari</strong></li>
            <li class="list-group-item d-flex justify-content-between"><span>Izin + Sakit:</span><strong>{{ $res['input']['jml_izin_sakit'] }} hari</strong></li>
            <li class="list-group-item d-flex justify-content-between"><span>Status Ekonomi:</span><strong>Kode {{ $res['input']['kode_ekonomi'] }}</strong></li>
            <li class="list-group-item d-flex justify-content-between"><span>Jarak Rumah:</span><strong>{{ $res['input']['jarak_km'] }} KM</strong></li>
            <li class="list-group-item d-flex justify-content-between"><span>Jml Saudara:</span><strong>{{ $res['input']['jml_saudara'] }}</strong></li>
          </ul>
          <small class="text-muted">Dianalisis menggunakan Model: {{ str_replace('_', ' ', $res['model']) }}</small>
        @else
          <div class="text-center py-5 text-muted">
            <i class="ti ti-calculator fs-9 text-muted mb-2 d-block"></i>
            Silakan isi nilai formulir di samping kiri lalu tekan <strong>Jalankan Simulasi Prediksi</strong> untuk melihat hasil analisis cerdas.
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
