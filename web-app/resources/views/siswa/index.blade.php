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
            <p class="card-subtitle mb-0">Kelola informasi demografi, sosio-ekonomi (KIP, KPS, PIP), dan profil peserta didik SMKN 1 Bungo</p>
          </div>
          <div class="mt-3 mt-md-0 d-flex gap-2">
            <!-- Modal Import Trigger -->
            <button type="button" class="btn btn-success d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalImportDapodik">
              <iconify-icon icon="solar:file-spreadsheet-linear" class="me-1 fs-5"></iconify-icon> Import Excel Dapodik
            </button>
            <!-- Modal Create Trigger -->
            <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
              <iconify-icon icon="solar:user-plus-rounded-linear" class="me-1 fs-5"></iconify-icon> Tambah Siswa
            </button>
          </div>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('siswa.index') }}" class="row g-3 mb-4 p-3 bg-light rounded-3 border">
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
                <th style="width: 50px;">No</th>
                <th>NISN / NIPD</th>
                <th>Nama Peserta Didik</th>
                <th>Kelas</th>
                <th>Sosial-Ekonomi</th>
                <th>Jarak</th>
                <th>Status Risiko</th>
                <th style="width: 130px; text-align: center;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($siswas as $idx => $s)
                <tr>
                  <td>{{ $siswas->firstItem() + $idx }}</td>
                  <td>
                    <span class="fw-bold text-dark">{{ $s->nisn }}</span><br>
                    <small class="text-muted">{{ $s->nipd ?? '-' }}</small>
                  </td>
                  <td>
                    <h6 class="fw-bold mb-0 text-dark">{{ $s->nama_lengkap }}</h6>
                    <small class="text-muted">JK: {{ $s->jenis_kelamin }} | Tanggungan: {{ $s->jml_saudara_kandung }} saudara</small>
                  </td>
                  <td>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $s->kelas->nama_kelas ?? '-' }}</span>
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
                      <span class="badge bg-light text-muted border">Reguler</span>
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
                    <div class="d-flex justify-content-center gap-1">
                      <!-- Modal Detail Button -->
                      <button type="button" class="btn btn-sm btn-info text-white p-1" data-bs-toggle="modal" data-bs-target="#modalDetailSiswa{{ $s->id }}" title="Lihat Detail">
                        <iconify-icon icon="solar:eye-linear"></iconify-icon>
                      </button>
                      <!-- Modal Edit Button -->
                      <button type="button" class="btn btn-sm btn-warning text-white p-1" data-bs-toggle="modal" data-bs-target="#modalEditSiswa{{ $s->id }}" title="Edit Data">
                        <iconify-icon icon="solar:pen-new-square-linear"></iconify-icon>
                      </button>
                      <!-- Modal Delete Button -->
                      <button type="button" class="btn btn-sm btn-danger p-1" data-bs-toggle="modal" data-bs-target="#modalHapusSiswa{{ $s->id }}" title="Hapus">
                        <iconify-icon icon="solar:trash-bin-trash-linear"></iconify-icon>
                      </button>
                    </div>
                  </td>
                </tr>

                <!-- 1. MODAL DETAIL SISWA -->
                <div class="modal fade" id="modalDetailSiswa{{ $s->id }}" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                      <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-dark">
                          <iconify-icon icon="solar:user-id-linear" class="me-1 fs-5 text-primary"></iconify-icon> Detail Data Siswa: {{ $s->nama_lengkap }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body p-4">
                        <div class="row g-3">
                          <div class="col-md-6 border-end">
                            <h6 class="fw-bold text-primary mb-3">Biodata Pokok (Dapodik)</h6>
                            <p class="mb-2"><strong>NISN:</strong> {{ $s->nisn }}</p>
                            <p class="mb-2"><strong>NIPD:</strong> {{ $s->nipd ?? '-' }}</p>
                            <p class="mb-2"><strong>Kelas / Rombel:</strong> {{ $s->kelas->nama_kelas ?? '-' }} ({{ $s->kelas->jurusan ?? '-' }})</p>
                            <p class="mb-2"><strong>Jenis Kelamin:</strong> {{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                            <p class="mb-2"><strong>Tempat, Tgl Lahir:</strong> {{ $s->tempat_lahir ?? '-' }}, {{ $s->tanggal_lahir ? (\Carbon\Carbon::parse($s->tanggal_lahir)->format('d/m/Y')) : '-' }}</p>
                            <p class="mb-2"><strong>Alamat:</strong> {{ $s->alamat ?? '-' }}</p>
                            <p class="mb-2"><strong>Jarak ke Sekolah:</strong> {{ $s->jarak_rumah_km }} KM</p>
                            <p class="mb-0"><strong>Transportasi:</strong> {{ $s->alat_transportasi ?? '-' }}</p>
                          </div>
                          <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">Sosial-Ekonomi &amp; Prediksi EWS</h6>
                            <p class="mb-2"><strong>Pekerjaan Ayah:</strong> {{ $s->pekerjaan_ayah ?? '-' }} (Rp {{ number_format($s->penghasilan_ayah) }})</p>
                            <p class="mb-2"><strong>Pekerjaan Ibu:</strong> {{ $s->pekerjaan_ibu ?? '-' }} (Rp {{ number_format($s->penghasilan_ibu) }})</p>
                            <p class="mb-2"><strong>Jumlah Saudara:</strong> {{ $s->jml_saudara_kandung }} orang</p>
                            <p class="mb-2">
                              <strong>Status Bantuan:</strong>
                              @if($s->is_penerima_kip) <span class="badge bg-success">KIP</span> @endif
                              @if($s->is_penerima_kps) <span class="badge bg-info">KPS</span> @endif
                              @if($s->is_layak_pip) <span class="badge bg-warning">Layak PIP</span> @endif
                              @if(!$s->is_penerima_kip && !$s->is_penerima_kps && !$s->is_layak_pip) <span class="badge bg-light text-muted">Reguler</span> @endif
                            </p>
                            <hr>
                            <div class="p-3 rounded {{ $s->latestPrediksi && $s->latestPrediksi->hasil_prediksi == 'Tinggi' ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                              <strong>Hasil Deteksi Cerdas:</strong>
                              <h5 class="fw-bold mb-1 {{ $s->latestPrediksi && $s->latestPrediksi->hasil_prediksi == 'Tinggi' ? 'text-danger' : 'text-success' }}">
                                Risiko {{ $s->latestPrediksi->hasil_prediksi ?? 'Belum Terdeteksi' }}
                                @if($s->latestPrediksi) ({{ number_format($s->latestPrediksi->probabilitas_risiko * 100, 1) }}%) @endif
                              </h5>
                              <small class="d-block">{{ $s->latestPrediksi->faktor_kunci ?? 'Belum ada faktor risiko yang dicatat.' }}</small>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer bg-light">
                        <a href="{{ route('siswa.show', $s->id) }}" class="btn btn-outline-primary btn-sm">Lihat Halaman Penuh &amp; Rapor</a>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- 2. MODAL EDIT SISWA -->
                <div class="modal fade" id="modalEditSiswa{{ $s->id }}" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                      <form action="{{ route('siswa.update', $s->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header bg-light">
                          <h5 class="modal-title fw-bold text-dark">
                            <iconify-icon icon="solar:pen-new-square-linear" class="me-1 fs-5 text-warning"></iconify-icon> Edit Siswa: {{ $s->nama_lengkap }}
                          </h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                          <div class="row g-3">
                            <div class="col-md-6">
                              <label class="form-label fw-bold">NISN <span class="text-danger">*</span></label>
                              <input type="text" name="nisn" class="form-control" value="{{ $s->nisn }}" required>
                            </div>
                            <div class="col-md-6">
                              <label class="form-label fw-bold">NIPD</label>
                              <input type="text" name="nipd" class="form-control" value="{{ $s->nipd }}">
                            </div>
                            <div class="col-md-6">
                              <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                              <input type="text" name="nama_lengkap" class="form-control" value="{{ $s->nama_lengkap }}" required>
                            </div>
                            <div class="col-md-3">
                              <label class="form-label fw-bold">Kelas <span class="text-danger">*</span></label>
                              <select name="kelas_id" class="form-select" required>
                                @foreach($kelasList as $k)
                                  <option value="{{ $k->id }}" {{ $s->kelas_id == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                @endforeach
                              </select>
                            </div>
                            <div class="col-md-3">
                              <label class="form-label fw-bold">Jenis Kelamin</label>
                              <select name="jenis_kelamin" class="form-select" required>
                                <option value="L" {{ $s->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ $s->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                              </select>
                            </div>
                            <div class="col-md-6">
                              <label class="form-label fw-bold">Tempat Lahir</label>
                              <input type="text" name="tempat_lahir" class="form-control" value="{{ $s->tempat_lahir }}">
                            </div>
                            <div class="col-md-6">
                              <label class="form-label fw-bold">Tanggal Lahir</label>
                              <input type="date" name="tanggal_lahir" class="form-control" value="{{ $s->tanggal_lahir ? (\Carbon\Carbon::parse($s->tanggal_lahir)->format('Y-m-d')) : '' }}">
                            </div>
                            <div class="col-12">
                              <label class="form-label fw-bold">Alamat</label>
                              <input type="text" name="alamat" class="form-control" value="{{ $s->alamat }}">
                            </div>
                            <div class="col-md-4">
                              <label class="form-label fw-bold">Jarak Rumah (KM)</label>
                              <input type="number" step="0.1" name="jarak_rumah_km" class="form-control" value="{{ $s->jarak_rumah_km }}">
                            </div>
                            <div class="col-md-4">
                              <label class="form-label fw-bold">Alat Transportasi</label>
                              <input type="text" name="alat_transportasi" class="form-control" value="{{ $s->alat_transportasi }}">
                            </div>
                            <div class="col-md-4">
                              <label class="form-label fw-bold">Status Keaktifan</label>
                              <select name="status_keaktifan" class="form-select" required>
                                <option value="Aktif" {{ $s->status_keaktifan == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Mutasi" {{ $s->status_keaktifan == 'Mutasi' ? 'selected' : '' }}>Mutasi</option>
                                <option value="Putus Sekolah" {{ $s->status_keaktifan == 'Putus Sekolah' ? 'selected' : '' }}>Putus Sekolah</option>
                                <option value="Lulus" {{ $s->status_keaktifan == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                              </select>
                            </div>
                            <div class="col-md-4">
                              <label class="form-label fw-bold">Penghasilan Ayah (Rp)</label>
                              <input type="number" name="penghasilan_ayah" class="form-control" value="{{ (int)$s->penghasilan_ayah }}">
                            </div>
                            <div class="col-md-4">
                              <label class="form-label fw-bold">Penghasilan Ibu (Rp)</label>
                              <input type="number" name="penghasilan_ibu" class="form-control" value="{{ (int)$s->penghasilan_ibu }}">
                            </div>
                            <div class="col-md-4">
                              <label class="form-label fw-bold">Jumlah Saudara</label>
                              <input type="number" name="jml_saudara_kandung" class="form-control" value="{{ $s->jml_saudara_kandung }}">
                            </div>
                            <div class="col-12">
                              <label class="form-label fw-bold d-block">Bantuan Sosial Afirmasi:</label>
                              <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="is_penerima_kip" id="editKip{{ $s->id }}" value="1" {{ $s->is_penerima_kip ? 'checked' : '' }}>
                                <label class="form-check-label" for="editKip{{ $s->id }}">Penerima KIP</label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="is_penerima_kps" id="editKps{{ $s->id }}" value="1" {{ $s->is_penerima_kps ? 'checked' : '' }}>
                                <label class="form-check-label" for="editKps{{ $s->id }}">Penerima KPS</label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="is_layak_pip" id="editPip{{ $s->id }}" value="1" {{ $s->is_layak_pip ? 'checked' : '' }}>
                                <label class="form-check-label" for="editPip{{ $s->id }}">Diusulkan Layak PIP</label>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer bg-light">
                          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                          <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <!-- 3. MODAL HAPUS SISWA -->
                <div class="modal fade" id="modalHapusSiswa{{ $s->id }}" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                      <form action="{{ route('siswa.destroy', $s->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header bg-danger-subtle">
                          <h5 class="modal-title fw-bold text-danger">Konfirmasi Hapus Data Siswa</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4 text-center">
                          <iconify-icon icon="solar:trash-bin-trash-bold" class="fs-10 text-danger mb-3"></iconify-icon>
                          <h6 class="fw-bold mb-2">Hapus data {{ $s->nama_lengkap }}?</h6>
                          <p class="text-muted fs-3 mb-0">NISN: <strong>{{ $s->nisn }}</strong> | Kelas: <strong>{{ $s->kelas->nama_kelas ?? '-' }}</strong><br>Seluruh histori rapor dan deteksi terkait juga akan terhapus permanen.</p>
                        </div>
                        <div class="modal-footer bg-light justify-content-center">
                          <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                          <button type="submit" class="btn btn-danger btn-sm px-3">Ya, Hapus Siswa</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

              @empty
                <tr>
                  <td colspan="8" class="text-center py-4 text-muted">Tidak ada data siswa ditemukan.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-4 d-flex justify-content-center">
          {{ $siswas->links() }}
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MODAL TAMBAH SISWA BARU -->
<div class="modal fade" id="modalTambahSiswa" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <form action="{{ route('siswa.store') }}" method="POST">
        @csrf
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title fw-bold text-white">
            <iconify-icon icon="solar:user-plus-rounded-linear" class="me-1 fs-5"></iconify-icon> Tambah Peserta Didik Baru
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">NISN <span class="text-danger">*</span></label>
              <input type="text" name="nisn" class="form-control" required placeholder="10 digit NISN">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">NIPD</label>
              <input type="text" name="nipd" class="form-control" placeholder="Nomor Induk PD">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Nama Lengkap Siswa <span class="text-danger">*</span></label>
              <input type="text" name="nama_lengkap" class="form-control" required placeholder="Nama lengkap siswa">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-bold">Kelas <span class="text-danger">*</span></label>
              <select name="kelas_id" class="form-select" required>
                @foreach($kelasList as $k)
                  <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-bold">Jenis Kelamin</label>
              <select name="jenis_kelamin" class="form-select" required>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Tempat Lahir</label>
              <input type="text" name="tempat_lahir" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Tanggal Lahir</label>
              <input type="date" name="tanggal_lahir" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label fw-bold">Alamat Rumah Lengkap</label>
              <input type="text" name="alamat" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-bold">Jarak Rumah (KM)</label>
              <input type="number" step="0.1" name="jarak_rumah_km" class="form-control" value="3.5">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-bold">Alat Transportasi</label>
              <input type="text" name="alat_transportasi" class="form-control" value="Sepeda Motor">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-bold">Jumlah Saudara</label>
              <input type="number" name="jml_saudara_kandung" class="form-control" value="2">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Penghasilan Ayah (Rp)</label>
              <input type="number" name="penghasilan_ayah" class="form-control" value="2000000">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Penghasilan Ibu (Rp)</label>
              <input type="number" name="penghasilan_ibu" class="form-control" value="0">
            </div>
            <div class="col-12">
              <label class="form-label fw-bold d-block">Status Bantuan Sosial:</label>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="is_penerima_kip" id="newKip" value="1">
                <label class="form-check-label" for="newKip">Penerima KIP</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="is_penerima_kps" id="newKps" value="1">
                <label class="form-check-label" for="newKps">Penerima KPS</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="is_layak_pip" id="newPip" value="1">
                <label class="form-check-label" for="newPip">Diusulkan Layak PIP</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary btn-sm">Simpan Siswa</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL IMPORT DAPODIK -->
<div class="modal fade" id="modalImportDapodik" tabindex="-1" aria-labelledby="modalImportDapodikLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title fw-bold text-white" id="modalImportDapodikLabel">
            <iconify-icon icon="solar:file-spreadsheet-linear" class="me-1 fs-5"></iconify-icon> Import Siswa dari Dapodik
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <p class="text-muted fs-3 mb-3">Pilih berkas Excel (.xlsx / .xls) unduhan Dapodik (misal: <code>Daftar PD 23-24.xlsx</code>). Sistem akan membaca NISN, Nama, Rombel, status KIP/KPS/PIP, dan Jarak Rumah secara otomatis.</p>
          <div class="mb-3">
            <label for="file_excel" class="form-label fw-bold">Pilih File Excel:</label>
            <input class="form-control" type="file" id="file_excel" name="file_excel" required>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success btn-sm" id="btnSubmitImport">
            <span class="spinner-border spinner-border-sm me-1 d-none" id="importSpinner" role="status" aria-hidden="true"></span>
            <span id="importBtnText">Mulai Import</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var formImport = document.querySelector('#modalImportDapodik form');
    if (formImport) {
      formImport.addEventListener('submit', function() {
        var btn = document.getElementById('btnSubmitImport');
        var spinner = document.getElementById('importSpinner');
        var text = document.getElementById('importBtnText');
        if (btn) btn.disabled = true;
        if (spinner) spinner.classList.remove('d-none');
        if (text) text.innerText = 'Mengimpor Data...';
      });
    }
  });
</script>
@endpush
@endsection
