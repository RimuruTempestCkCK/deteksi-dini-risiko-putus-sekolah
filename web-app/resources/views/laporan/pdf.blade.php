<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Laporan Deteksi Dini Risiko Putus Sekolah SMKN 1 Bungo</title>
  <style>
    body { font-family: sans-serif; font-size: 11px; color: #333; margin: 20px; }
    .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 15px; }
    .header h2 { margin: 0; font-size: 16px; text-transform: uppercase; }
    .header p { margin: 3px 0; font-size: 11px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #777; padding: 6px 8px; text-align: left; }
    th { background-color: #f2f2f2; font-weight: bold; }
    .badge-tinggi { color: #d9534f; font-weight: bold; }
    .badge-rendah { color: #5cb85c; font-weight: bold; }
    .footer-sign { margin-top: 30px; width: 100%; }
    .footer-sign td { border: none; padding: 0; }
  </style>
</head>
<body>
  <div class="header">
    <h2>PEMERINTAH PROVINSI JAMBI - DINAS PENDIDIKAN</h2>
    <h2>SMK NEGERI 1 BUNGO</h2>
    <p>Kecamatan Bathin III, Kabupaten Bungo, Provinsi Jambi</p>
    <p><strong>LAPORAN DETEKSI DINI RISIKO SISWA PUTUS SEKOLAH (EARLY WARNING SYSTEM)</strong></p>
    <small>Metode Klasifikasi: {{ str_replace('_', ' ', $activeModel->algoritma ?? 'Random Forest') }} | Akurasi Model: {{ $activeModel->akurasi ?? 0 }}%</small>
  </div>

  @if($filterRisiko)
    <p><em>Menampilkan hanya data peserta didik dengan Kategori Risiko: <strong>{{ $filterRisiko }}</strong></em></p>
  @endif

  <table>
    <thead>
      <tr>
        <th style="width: 25px;">No</th>
        <th style="width: 75px;">NISN</th>
        <th>Nama Peserta Didik</th>
        <th style="width: 80px;">Kelas / Jurusan</th>
        <th style="width: 60px;">Bantuan PIP</th>
        <th style="width: 50px;">Jarak (KM)</th>
        <th style="width: 80px;">Hasil Deteksi</th>
        <th style="width: 65px;">Probabilitas</th>
        <th>Faktor Risiko Kunci</th>
      </tr>
    </thead>
    <tbody>
      @forelse($siswas as $idx => $s)
        <tr>
          <td>{{ $idx + 1 }}</td>
          <td>{{ $s->nisn }}</td>
          <td><strong>{{ $s->nama_lengkap }}</strong></td>
          <td>{{ $s->kelas->nama_kelas ?? '-' }}</td>
          <td>{{ ($s->is_layak_pip || $s->is_penerima_kip) ? 'Ya' : 'Tidak' }}</td>
          <td>{{ $s->jarak_rumah_km }} KM</td>
          <td>
            @if($s->latestPrediksi && $s->latestPrediksi->hasil_prediksi === 'Tinggi')
              <span class="badge-tinggi">TINGGI</span>
            @else
              <span class="badge-rendah">AMAN</span>
            @endif
          </td>
          <td>{{ $s->latestPrediksi ? number_format($s->latestPrediksi->probabilitas_risiko * 100, 1) . '%' : '-' }}</td>
          <td>{{ $s->latestPrediksi->faktor_kunci ?? '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="9" style="text-align: center;">Tidak ada data yang sesuai kriteria.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <table class="footer-sign">
    <tr>
      <td style="width: 65%;"></td>
      <td style="width: 35%; text-align: center;">
        <p>Muara Bungo, {{ date('d F Y') }}</p>
        <p>Mengetahui,<br><strong>Kepala SMK Negeri 1 Bungo</strong></p>
        <br><br><br>
        <p><u>( .................................................. )</u><br>NIP. .........................................</p>
      </td>
    </tr>
  </table>
</body>
</html>
