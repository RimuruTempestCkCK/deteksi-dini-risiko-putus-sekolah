@extends('layouts.app')

@section('title', 'Edit Data Siswa - SMKN 1 Bungo')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-4">Edit Data Peserta Didik: {{ $siswa->nama_lengkap }}</h5>

        <form action="{{ route('siswa.update', $siswa->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Nomor Induk Siswa Nasional (NISN) <span class="text-danger">*</span></label>
              <input type="text" name="nisn" class="form-control @error('nisn') is-invalid @enderror" value="{{ old('nisn', $siswa->nisn) }}" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">NIPD</label>
              <input type="text" name="nipd" class="form-control" value="{{ old('nipd', $siswa->nipd) }}">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Nama Lengkap Siswa <span class="text-danger">*</span></label>
              <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}" required>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label fw-bold">Kelas / Rombel <span class="text-danger">*</span></label>
              <select name="kelas_id" class="form-select" required>
                @foreach($kelasList as $k)
                  <option value="{{ $k->id }}" {{ $siswa->kelas_id == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }} ({{ $k->jurusan }})</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label fw-bold">Jenis Kelamin <span class="text-danger">*</span></label>
              <select name="jenis_kelamin" class="form-select" required>
                <option value="L" {{ $siswa->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ $siswa->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Tempat Lahir</label>
              <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Tanggal Lahir</label>
              <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir ? $siswa->tanggal_lahir->format('Y-m-d') : '') }}">
            </div>
            <div class="col-12 mb-3">
              <label class="form-label fw-bold">Alamat Rumah Lengkap</label>
              <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $siswa->alamat) }}</textarea>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label fw-bold">Jarak Rumah ke Sekolah (KM)</label>
              <input type="number" step="0.1" name="jarak_rumah_km" class="form-control" value="{{ old('jarak_rumah_km', $siswa->jarak_rumah_km) }}">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label fw-bold">Alat Transportasi</label>
              <input type="text" name="alat_transportasi" class="form-control" value="{{ old('alat_transportasi', $siswa->alat_transportasi) }}">
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label fw-bold">Status Keaktifan</label>
              <select name="status_keaktifan" class="form-select" required>
                <option value="Aktif" {{ $siswa->status_keaktifan == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Mutasi" {{ $siswa->status_keaktifan == 'Mutasi' ? 'selected' : '' }}>Mutasi</option>
                <option value="Putus Sekolah" {{ $siswa->status_keaktifan == 'Putus Sekolah' ? 'selected' : '' }}>Putus Sekolah</option>
                <option value="Lulus" {{ $siswa->status_keaktifan == 'Lulus' ? 'selected' : '' }}>Lulus</option>
              </select>
            </div>

            <div class="col-12"><hr><h6 class="fw-bold mb-3">Data Ekonomi Orang Tua &amp; Bantuan Sosial</h6></div>

            <div class="col-md-6 mb-3">
              <label class="form-label">Penghasilan Ayah (Rp)</label>
              <input type="number" name="penghasilan_ayah" class="form-control" value="{{ old('penghasilan_ayah', (int)$siswa->penghasilan_ayah) }}">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Penghasilan Ibu (Rp)</label>
              <input type="number" name="penghasilan_ibu" class="form-control" value="{{ old('penghasilan_ibu', (int)$siswa->penghasilan_ibu) }}">
            </div>
            <div class="col-12 mb-4">
              <label class="form-label fw-bold d-block">Kepemilikan Bantuan Sosial:</label>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="is_penerima_kip" id="kip" value="1" {{ $siswa->is_penerima_kip ? 'checked' : '' }}>
                <label class="form-check-label" for="kip">Penerima KIP</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="is_penerima_kps" id="kps" value="1" {{ $siswa->is_penerima_kps ? 'checked' : '' }}>
                <label class="form-check-label" for="kps">Penerima KPS</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="is_layak_pip" id="pip" value="1" {{ $siswa->is_layak_pip ? 'checked' : '' }}>
                <label class="form-check-label" for="pip">Diusulkan Layak PIP</label>
              </div>
            </div>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i> Perbarui Data</button>
            <a href="{{ route('siswa.index') }}" class="btn btn-outline-secondary">Batal</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
