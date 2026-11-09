# LAPORAN HASIL PEMBERSIHAN KODE LARAVEL (POST-CLEANUP)
## Sistem Deteksi Dini Risiko Siswa Putus Sekolah — SMKN 1 Bungo

**Auditor & Pelaksana**: Senior Laravel Developer, Data Scientist & QA Engineer  
**Tanggal**: 9 November 2026  
**Status Akhir**: `CLEANUP VERIFIED`  

---

## 1. Ringkasan Eksekutif Pembersihan
Sesuai instruksi pada `AGY_CLI_AUDIT_CLEANUP_LARAVEL.md`:
1. Telah dilakukan audit menyeluruh terhadap 10 controller, 4 services machine learning, 8 models, 17 views, seluruh routes, dan dependencies.
2. Dihapus **hanya kode/file yang 100% terbukti tidak digunakan dan aman dihapus**:
   - Berkas view default boilerplate Laravel: `web-app/resources/views/welcome.blade.php`.
3. **Seluruh fitur penelitian KDD dan kesetaraan (*parity*) Python ↔ Laravel dipertahankan sepenuhnya**:
   - Ekstraksi 8 Fitur Prediktor ($X_1$ s.d. $X_8$).
   - Evaluasi inferensi Random Forest (100 Trees ensemble, majority voting).
   - Evaluasi inferensi Gaussian Naïve Bayes (Gaussian likelihood density function, log joint probability, dan posterior MAP).
   - Metrik Confusion Matrix (Akurasi, Presisi, Recall, F1-Score pada 214 data testing).
   - Halaman simulasi kalkulator risiko siswa interaktif.

---

## 2. File yang Diperiksa & Dihapus

### A. File yang Dihapus (`SAFE_REMOVE`)
- `web-app/resources/views/welcome.blade.php`  
  *Alasan*: Template bawaan instalasi awal Laravel (133 baris HTML/CSS Tailwind inline) yang tidak pernah dirender oleh controller maupun route aplikasi (`/` telah diarahkan langsung ke `DashboardController@index`).

### B. Komponen yang Dipertahankan (`KEEP`)
- Seluruh 4 Machine Learning Services:
  - `PreprocessingService.php` (8 fitur dan ordinal encoding).
  - `RandomForestService.php` (100 Decision Trees model evaluator).
  - `NaiveBayesService.php` (Gaussian Naive Bayes model evaluator).
  - `EvaluationMetricsService.php` (kalkulator confusion matrix).
- Seluruh 10 Controllers (`DashboardController`, `MiningController`, `KomparasiController`, `PredictionController`, `NilaiPresensiController`, `SiswaController`, `IntervensiController`, `LaporanExportController`, `AuthController`, `Controller`).
- Seluruh Routes aktif (33 routes) di `routes/web.php` dan `routes/api.php`.
- Seluruh dokumen audit dan validasi parity di `web-app/docs/`.

---

## 3. Hasil Verifikasi Pasca-Pembersihan

### A. Linter Sintaks PHP
- Seluruh file PHP di dalam `app/`, `routes/`, dan `tests/` diperiksa dengan `php -l`.
- **Hasil**: `No syntax errors detected` (100% Valid).

### B. Route Validation
- Perintah `php artisan route:list` dijalankan.
- **Hasil**: 33 route terdaftar aktif dan terhubung sempurna ke controller method masing-masing.

### C. Verifikasi Parity Python ↔ Laravel (214 Data Uji)
Dijalankan secara terpisah melalui dua script pengujian:
1. `web-app/tests/verify_parity.php`
2. `audit_independen/run_laravel_eval.php` (membaca langsung baris per baris dari sheet `03_DATA_SPLIT` di `PERHITUNGAN_MANUAL.xlsx`)

| Model | Metrik Python Baseline | Metrik Laravel Pasca-Cleanup | Selisih | Status Parity |
| :--- | :--- | :--- | :--- | :--- |
| **Random Forest** | Akurasi: 99.53% | Akurasi: 99.53% | 0.00% | **MATCH (Identik)** |
| | Presisi: 100.00% | Presisi: 100.00% | 0.00% | **MATCH (Identik)** |
| | Recall: 85.71% | Recall: 85.71% | 0.00% | **MATCH (Identik)** |
| | F1-Score: 92.31% | F1-Score: 92.31% | 0.00% | **MATCH (Identik)** |
| | Confusion Matrix: TN=207, FP=0, FN=1, TP=6 | Confusion Matrix: TN=207, FP=0, FN=1, TP=6 | 0 | **MATCH (Identik)** |
| **Gaussian Naïve Bayes** | Akurasi: 98.13% | Akurasi: 98.13% | 0.00% | **MATCH (Identik)** |
| | Presisi: 66.67% | Presisi: 66.67% | 0.00% | **MATCH (Identik)** |
| | Recall: 85.71% | Recall: 85.71% | 0.00% | **MATCH (Identik)** |
| | F1-Score: 75.00% | F1-Score: 75.00% | 0.00% | **MATCH (Identik)** |
| | Confusion Matrix: TN=204, FP=3, FN=1, TP=6 | Confusion Matrix: TN=204, FP=3, FN=1, TP=6 | 0 | **MATCH (Identik)** |

- **Sampel Uji D1 (Andini Febryanti - NISN 0069545198)**:
  - Random Forest: **Tinggi** (Probabilitas: $62.55\%$) $\rightarrow$ **MATCH**
  - Naïve Bayes: **Tinggi** (Probabilitas Posterior: $97.82\%$) $\rightarrow$ **MATCH**

---

## 4. Kesimpulan Status Akhir
Status: **`CLEANUP VERIFIED`**  
Pembersihan telah dilakukan secara aman, bersih, dan minimal tanpa merusak arsitektur aplikasi maupun hasil matematis penelitian. Seluruh fitur inti dan kesetaraan komparasi model machine learning antara Python, Excel, dan Laravel tetap **100% konsisten dan terverifikasi**.
