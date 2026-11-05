---
name: early-warning-system-integration
description: >-
  Integrasi model machine learning ke dalam arsitektur web-app Laravel, manajemen database,
  dashboard analitik MatDash, simulasi interaktif, dan ekspor laporan PDF hasil deteksi dini.
---

# Skill: Integrasi Sistem Deteksi Dini Risiko Putus Sekolah (Web App)

Skill ini mengatur implementasi teknis dan integrasi model data mining ke dalam platform aplikasi web deteksi dini berbasis Laravel dan template MatDash.

## 1. Arsitektur Komponen Web-App (`web-app/`)
- **Controller & Service Layer**:
  - `MiningController.php`: Orkestrasi proses data mining, import dataset Dapodik/Leger, dan eksekusi algoritma.
  - `RandomForestService.php`: Engine komputasi ensemble pohon keputusan.
  - `NaiveBayesService.php`: Engine probabilitas Bayes.
  - `PrediksiController.php` & `IntervensiController.php`: Modul penanganan prediksi individual/massal dan rekomendasi tindakan preventif guru BK/Wali Kelas.
- **Views & UI (`resources/views/`)**:
  - `dashboard/index.blade.php`: Ringkasan KPI dan grafik persebaran risiko.
  - `komparasi/index.blade.php`: Tabel dan visualisasi perbandingan RF vs NB.
  - `prediksi/simulasi.blade.php`: Form simulasi input interaktif dengan hasil modal pop-up.
  - `laporan/index.blade.php` & `laporan/pdf.blade.php`: Cetak rekapitulasi data siswa berisiko ke format dokumen PDF.

## 2. Standard Response & UX
1. **Modal Konfirmasi & Feedback**:
   - Seluruh aksi CRUD dan komputasi berat dilengkapi modal konfirmasi dan indikator loading spinner.
   - Mengalokasikan `memory_limit` dan `max_execution_time` memadai untuk data berukuran ribuan baris.
2. **Keterbacaan Hasil**:
   - Tampilkan badge status risiko dengan indikator warna visual: Merah (*Tinggi*), Kuning/Oranye (*Sedang*), Hijau (*Rendah*).
   - Sertakan persentase keyakinan prediksi (*confidence score* / probabilitas kelas).
