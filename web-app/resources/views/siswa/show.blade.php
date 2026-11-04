@extends('layouts.app')

@section('title', 'Detail Siswa: ' . $siswa->nama_lengkap)

@section('content')
<div class="row">
  <!-- Profil Siswa Card -->
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body text-center p-4">
        <div class="d-inline-flex p-3 rounded-circle bg-primary-subtle text-primary mb-3">
          <i class="ti ti-user fs-9"></i>
        </div>
        <h4 class="fw-bold mb-1">{{ $siswa->nama_lengkap }}</h4>
        <span class="badge bg-primary-subtle text-primary fs-3 px-3 py-1 mb-3">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
        
        <div class="text-start border-top pt-3 mt-3">
          <div class="mb-2"><strong>NISN:</strong> {{ $siswa->nisn }}</div>
          <div class="mb-2"><strong>NIPD:</strong> {{ $siswa->nipd ?? '-' }}</div>
          <div class="mb-2"><strong>Jenis Kelamin:</strong> {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
          <div class="mb-2"><strong>Tempat, Tgl Lahir:</strong> {{ $siswa->tempat_lahir ?? '-' }}, {{ $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('d/m/Y') : '-' }}</div>
          <div class="mb-2"><strong>Alamat:</strong> {{ $siswa->alamat ?? '-' }}</div>
          <div class="mb-2"><strong>Jarak ke Sekolah:</strong> {{ $siswa->jarak_rumah_km }} KM</div>
          <div class="mb-2"><strong>Transportasi:</strong> {{ $siswa->alat_transportasi ?? '-' }}</div>
          <div class="mb-2"><strong>Status Keaktifan:</strong> <span class="badge bg-success">{{ $siswa->status_keaktifan }}</span></div>
        </div>
      </div>
    </div>

    <!-- Kondisi Sosio-Ekonomi -->
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">Kondisi Sosial-Ekonomi Ortu</h5>
        <ul class="list-unstyled mb-0">
          <li class="py-2 border-bottom"><strong>Pekerjaan Ayah:</strong> {{ $siswa->pekerjaan_ayah ?? '-' }}</li>
          <li class="py-2 border-bottom"><strong>Penghasilan Ayah:</strong> Rp {{ number_format($siswa->penghasilan_ayah) }}</li>
          <li class="py-2 border-bottom"><strong>Pekerjaan Ibu:</strong> {{ $siswa->pekerjaan_ibu ?? '-' }}</li>
          <li class="py-2 border-bottom"><strong>Penghasilan Ibu:</strong> Rp {{ number_format($siswa->penghasilan_ibu) }}</li>
          <li class="py-2 border-bottom"><strong>Jumlah Saudara:</strong> {{ $siswa->jml_saudara_kandung }} orang</li>
          <li class="py-2"><strong>Bantuan Sosial:</strong>
            @if($siswa->is_penerima_kip) <span class="badge bg-success">KIP</span> @endif
            @if($siswa->is_penerima_kps) <span class="badge bg-info">KPS</span> @endif
            @if($siswa->is_layak_pip) <span class="badge bg-warning">Layak PIP</span> @endif
            @if(!$siswa->is_penerima_kip && !$siswa->is_penerima_kps && !$siswa->is_layak_pip) <span class="badge bg-light text-muted">Tidak Ada</span> @endif
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <!-- Status Deteksi Terkini -->
    @if($siswa->latestPrediksi)
      <div class="card {{ $siswa->latestPrediksi->hasil_prediksi == 'Tinggi' ? 'border-danger' : 'border-success' }} border-2">
        <div class="card-body p-4">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <span class="text-muted fs-3">Hasil Deteksi Dini Sistem Cerdas:</span>
              <h3 class="fw-bold mb-0 {{ $siswa->latestPrediksi->hasil_prediksi == 'Tinggi' ? 'text-danger' : 'text-success' }}">
                Risiko {{ $siswa->latestPrediksi->hasil_prediksi }}
              </h3>
            </div>
            <div class="text-end">
              <span class="fs-2 text-muted">Probabilitas Risiko</span>
              <h3 class="fw-bolder mb-0 {{ $siswa->latestPrediksi->hasil_prediksi == 'Tinggi' ? 'text-danger' : 'text-success' }}">
                {{ number_format($siswa->latestPrediksi->probabilitas_risiko * 100, 1) }}%
              </h3>
            </div>
          </div>
          <hr>
          <div class="mb-0">
            <strong>Faktor Kunci Risiko:</strong>
            <p class="text-muted mb-0">{{ $siswa->latestPrediksi->faktor_kunci }}</p>
          </div>
          <small class="text-muted d-block mt-2">Dianalisis menggunakan Model: {{ str_replace('_', ' ', $siswa->latestPrediksi->modelEvaluasi->algoritma ?? 'Random Forest') }} pada {{ $siswa->latestPrediksi->tanggal_prediksi->format('d F Y') }}</small>
        </div>
      </div>
    @endif

    <!-- Riwayat Rapor Semester -->
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">Histori Akademik &amp; Presensi Leger Rapor</h5>
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>Tahun / Semester</th>
                <th>Rerata Nilai</th>
                <th>Mapel &lt; KKM</th>
                <th>Sakit (S)</th>
                <th>Izin (I)</th>
                <th>Alfa (A)</th>
              </tr>
            </thead>
            <tbody>
              @forelse($siswa->raporSemesters as $r)
                <tr>
                  <td>{{ $r->tahun_ajaran }} ({{ $r->semester }})</td>
                  <td class="fw-bold {{ $r->rerata_nilai < 75 ? 'text-danger' : 'text-success' }}">{{ $r->rerata_nilai }}</td>
                  <td>{{ $r->jml_mapel_dibawah_kkm }} mapel</td>
                  <td>{{ $r->total_sakit }} hari</td>
                  <td>{{ $r->total_izin }} hari</td>
                  <td class="fw-bold {{ $r->total_alfa >= 10 ? 'text-danger' : '' }}">{{ $r->total_alfa }} hari</td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted">Belum ada data nilai rapor untuk siswa ini.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Tindakan Intervensi -->
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="card-title fw-semibold mb-0">Catatan Tindak Lanjut &amp; Intervensi</h5>
          <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahIntervensi">
            <i class="ti ti-plus"></i> Tambah Tindakan
          </button>
        </div>

        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Jenis Tindakan</th>
                <th>Catatan Penanganan</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($siswa->tindakanIntervensis as $t)
                <tr>
                  <td>{{ $t->tgl_tindakan->format('d/m/Y') }}</td>
                  <td class="fw-semibold">{{ $t->jenis_tindakan }}</td>
                  <td>{{ $t->catatan_penanganan }}</td>
                  <td>
                    <span class="badge {{ $t->status_intervensi == 'Selesai/Aman' ? 'bg-success' : 'bg-warning' }}">
                      {{ $t->status_intervensi }}
                    </span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted py-3">Belum ada catatan intervensi.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah Intervensi -->
<div class="modal fade" id="modalTambahIntervensi" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('intervensi.store') }}" method="POST">
        @csrf
        <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
        <div class="modal-header">
          <h5 class="modal-title fw-semibold">Tambah Tindakan Intervensi Siswa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Tanggal Tindakan</label>
            <input type="date" name="tgl_tindakan" class="form-control" value="{{ date('Y-m-d') }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Jenis Tindakan</label>
            <select name="jenis_tindakan" class="form-select" required>
              <option value="Konseling Individual Siswa">Konseling Individual Siswa</option>
              <option value="Pemanggilan Orang Tua / Wali">Pemanggilan Orang Tua / Wali</option>
              <option value="Home Visit (Kunjungan Rumah)">Home Visit (Kunjungan Rumah)</option>
              <option value="Verifikasi &amp; Usulan Bantuan PIP / Finansial">Verifikasi &amp; Usulan Bantuan PIP / Finansial</option>
              <option value="Program Remedial Khusus Mata Pelajaran">Program Remedial Khusus Mata Pelajaran</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Catatan Penanganan &amp; Solusi</label>
            <textarea name="catatan_penanganan" class="form-control" rows="3" placeholder="Tuliskan hasil diskusi, komitmen siswa, atau kesepakatan orang tua..." required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Status Intervensi</label>
            <select name="status_intervensi" class="form-select" required>
              <option value="Pending">Pending</option>
              <option value="Dalam Pemantauan" selected>Dalam Pemantauan</option>
              <option value="Selesai/Aman">Selesai/Aman</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Intervensi</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
