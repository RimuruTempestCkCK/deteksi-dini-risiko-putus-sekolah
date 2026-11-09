# LAPORAN AUDIT PEMBERSIHAN KODE LARAVEL (PRE-CLEANUP)
## Sistem Deteksi Dini Risiko Siswa Putus Sekolah — SMKN 1 Bungo

**Auditor**: Senior Laravel Developer, Data Scientist & QA Engineer  
**Tanggal**: 9 November 2026  
**Status Audit**: READ-ONLY COMPLETE  

---

## 1. Ringkasan Inventaris Proyek
- **Aplikasi Web**: Laravel Framework 10.50.4, PHP 8.5.12, Composer 2.x
- **Total Route Terdaftar**: 33 Route (termasuk Auth, Dashboard, Siswa, Leger Nilai, KDD Mining, Komparasi RF vs NB, Prediksi & Simulasi, Intervensi, dan Ekspor Laporan PDF)
- **Total Controller**: 10 Controller
- **Total Machine Learning Services**: 4 Service (`EvaluationMetricsService`, `NaiveBayesService`, `PreprocessingService`, `RandomForestService`)
- **Total Eloquent Models**: 8 Model (`FiturDataset`, `Kelas`, `ModelEvaluasi`, `PrediksiLog`, `RaporSemester`, `Siswa`, `TindakanIntervensi`, `User`)
- **Total Views**: 17 Template Blade (termasuk partial layout MatDash UI)

---

## 2. Fitur Inti Penelitian yang Wajib Dipertahankan (`KEEP`)
Sesuai dengan implementasi Python (`hitung_manual_kdd.py`) dan workbook (`PERHITUNGAN_MANUAL.xlsx`):
1. **Pembersihan Data & 8 Fitur Prediktor**:
   - $X_1$ (Rerata Nilai Rapor), $X_2$ (Mapel $<$ KKM), $X_3$ (Alfa), $X_4$ (Izin & Sakit), $X_5$ (Status Ekonomi 1–3), $X_6$ (Jarak KM), $X_7$ (Pelanggaran Disiplin), $X_8$ (Skor Kerentanan Dukungan & Ekskul 1–3).
2. **Mesin Inferensi Random Forest**:
   - Evaluasi ensemble 100 Decision Trees dengan majority voting dan presisi float32.
3. **Mesin Inferensi Gaussian Naïve Bayes**:
   - Evaluasi log joint probability, fungsi densitas Gaussian likelihood, dan normalisasi posterior MAP.
4. **Metrik Komparasi & Confusion Matrix**:
   - Matriks konfusi pada 214 data testing: Akurasi (RF $99.53\%$, NB $98.13\%$), Presisi (RF $100\%$, NB $66.67\%$), Recall ($85.71\%$), dan F1-Score (RF $92.31\%$, NB $75.00\%$).
5. **Halaman Simulasi Kalkulator Risiko Siswa**:
   - Form interaktif pengujian single-sample berbasis 8 fitur input dengan komparasi langsung RF vs NB.

---

## 3. Daftar Kandidat Kode Tidak Terpakai & Mismatch

| No | File / Komponen | Kategori | Bukti Aktual | Dampak jika Dihapus | Status Rekomendasi |
| :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | `web-app/resources/views/welcome.blade.php` | Boilerplate Tidak Terpakai | Tidak direferensikan dalam `web.php` (`/` mengarah ke `DashboardController@index`). Merupakan default starter view Laravel. | Nol. Tidak ada view, controller, atau route yang memanggil `welcome`. | `SAFE_REMOVE` |
| 2 | `routes/api.php` (route `/user`) | Default Boilerplate | Tidak digunakan frontend MatDash (berbasis session web Laravel). | Tidak mengganggu fungsionalitas web penelitian. Namun file route inti sebaiknya dibiarkan kosong atau dipertahankan minimal. | `KEEP` (Biar file struktur Laravel standar tetap ada) |
| 3 | `routes/channels.php` | Default Boilerplate | Aplikasi tidak menggunakan event broadcasting / websockets. | Tidak ada pemanggilan channel. | `KEEP` (Bagian scaffolding Laravel) |
| 4 | File prompt duplikat di root: `AGY_CLI_AUDIT_CLEANUP_LARAVEL.md` & `web-app/AGY_CLI_PROMPT_LARAVEL_PARITY.md` | Dokumentasi Instruksi | File panduan prompt CLI. | Aman dipertahankan sebagai dokumentasi jejak audit. | `KEEP` |
| 5 | Database seeders dummy (`SiswaSeeder.php` 12 data contoh) | Data Seeder | Dipakai jika me-reset database lokal developer. | Bukan bagian runtime produksi. | `KEEP` |

---

## 4. Rencana Tindakan Pembersihan Minimal
1. **Menghapus berkas yang terbukti 100% tidak terpakai**:
   - `web-app/resources/views/welcome.blade.php` (View default Laravel 133 baris tanpa referensi).
2. **Tidak melakukan refaktor agresif** pada struktur service atau controller agar stabilitas route dan parity tidak terganggu.
3. **Menjalankan verifikasi ulang**:
   - Linter sintaks PHP
   - Route list check
   - Parity verification test (`php web-app/tests/verify_parity.php`)
