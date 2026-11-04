@extends('layouts.app')

@section('title', 'Tindakan Intervensi Siswa - SMKN 1 Bungo')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-md-flex align-items-center justify-content-between mb-4">
          <div>
            <h5 class="card-title fw-semibold">Pencatatan &amp; Pemantauan Tindakan Intervensi</h5>
            <p class="card-subtitle mb-0">Tindak lanjut langsung oleh Admin terhadap peserta didik yang terdeteksi berisiko putus sekolah</p>
          </div>
          <div class="mt-3 mt-md-0">
            <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalTambahIntervensi">
              <i class="ti ti-plus me-1 fs-5"></i> Catat Intervensi Baru
            </button>
          </div>
        </div>

        <!-- Filter Status -->
        <form method="GET" action="{{ route('intervensi.index') }}" class="row g-3 mb-4 p-3 bg-light rounded">
          <div class="col-md-6">
            <select name="status_intervensi" class="form-select">
              <option value="">-- Semua Status Intervensi --</option>
              <option value="Pending" {{ request('status_intervensi') == 'Pending' ? 'selected' : '' }}>Pending</option>
              <option value="Dalam Pemantauan" {{ request('status_intervensi') == 'Dalam Pemantauan' ? 'selected' : '' }}>Dalam Pemantauan</option>
              <option value="Selesai/Aman" {{ request('status_intervensi') == 'Selesai/Aman' ? 'selected' : '' }}>Selesai / Teratasi</option>
            </select>
          </div>
          <div class="col-md-6 d-flex gap-2">
            <button type="submit" class="btn btn-secondary w-50">Filter</button>
            <a href="{{ route('intervensi.index') }}" class="btn btn-outline-secondary w-50">Reset</a>
          </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Tanggal</th>
                <th>Nama Peserta Didik</th>
                <th>Kelas</th>
                <th>Jenis Intervensi</th>
                <th>Catatan Solusi</th>
                <th>Status</th>
                <th>Aksi Perubahan</th>
              </tr>
            </thead>
            <tbody>
              @forelse($intervensis as $item)
                <tr>
                  <td>{{ $item->tgl_tindakan->format('d/m/Y') }}</td>
                  <td>
                    <h6 class="fw-bold mb-0">{{ $item->siswa->nama_lengkap ?? '-' }}</h6>
                    <small class="text-muted">NISN: {{ $item->siswa->nisn ?? '-' }}</small>
                  </td>
                  <td>{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>
                  <td class="fw-semibold text-primary">{{ $item->jenis_tindakan }}</td>
                  <td><small>{{ $item->catatan_penanganan }}</small></td>
                  <td>
                    @if($item->status_intervensi === 'Selesai/Aman')
                      <span class="badge bg-success">Selesai/Aman</span>
                    @elseif($item->status_intervensi === 'Dalam Pemantauan')
                      <span class="badge bg-warning">Dalam Pemantauan</span>
                    @else
                      <span class="badge bg-secondary">Pending</span>
                    @endif
                  </td>
                  <td>
                    <form action="{{ route('intervensi.status', $item->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('PATCH')
                      <select name="status_intervensi" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                        <option value="Pending" {{ $item->status_intervensi == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Dalam Pemantauan" {{ $item->status_intervensi == 'Dalam Pemantauan' ? 'selected' : '' }}>Dalam Pemantauan</option>
                        <option value="Selesai/Aman" {{ $item->status_intervensi == 'Selesai/Aman' ? 'selected' : '' }}>Selesai/Aman</option>
                      </select>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-4 text-muted">Belum ada catatan intervensi.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-4">
          {{ $intervensis->links() }}
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
        <div class="modal-header">
          <h5 class="modal-title fw-semibold">Catat Tindakan Intervensi Siswa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-bold">Pilih Siswa yang Berisiko</label>
            <select name="siswa_id" class="form-select" required>
              <option value="">-- Pilih Siswa --</option>
              @foreach($siswasBerisiko as $sb)
                <option value="{{ $sb->id }}">{{ $sb->nama_lengkap }} ({{ $sb->kelas->nama_kelas ?? '-' }})</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Tanggal Tindakan</label>
            <input type="date" name="tgl_tindakan" class="form-control" value="{{ date('Y-m-d') }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Jenis Tindakan Intervensi</label>
            <select name="jenis_tindakan" class="form-select" required>
              <option value="Konseling Individual Motivasi Belajar">Konseling Individual Motivasi Belajar</option>
              <option value="Pemanggilan Orang Tua / Wali Murid">Pemanggilan Orang Tua / Wali Murid</option>
              <option value="Home Visit (Kunjungan Rumah)">Home Visit (Kunjungan Rumah)</option>
              <option value="Pengajuan Bantuan Afirmasi PIP / Komite">Pengajuan Bantuan Afirmasi PIP / Komite</option>
              <option value="Program Penguatan Akademik / Remedial">Program Penguatan Akademik / Remedial</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Catatan Penanganan &amp; Solusi</label>
            <textarea name="catatan_penanganan" class="form-control" rows="3" placeholder="Deskripsikan komitmen kehadiran, hasil kunjungan, atau kesepakatan penanganan..." required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Status Awal</label>
            <select name="status_intervensi" class="form-select" required>
              <option value="Dalam Pemantauan" selected>Dalam Pemantauan</option>
              <option value="Pending">Pending</option>
              <option value="Selesai/Aman">Selesai/Aman</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Tindakan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
