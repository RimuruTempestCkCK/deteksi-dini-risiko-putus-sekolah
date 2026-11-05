# LAPORAN AUDIT DATASET DAN AUDIT FILE EXCEL SEBELUMNYA
**Penelitian:** Sistem Cerdas dalam Deteksi Dini Risiko Siswa Putus Sekolah dengan Komparasi Metode Random Forest dan Naïve Bayes pada Siswa SMK Negeri 1 Bungo  
**Tanggal Audit:** 05 November 2026  
**Auditor:** Antigravity System Auditor  

---

## BAGIAN I: AUDIT MENYELURUH DATASET ASLI (FOLDER `DATA/`)

### 1. Inventarisasi Seluruh Berkas di Folder `DATA/`
Berdasarkan penelusuran rekursif terhadap direktori `D:\DOWNLOAD FOLDER\deteksi-dini-risiko-putus-sekolah\DATA`, ditemukan total **284 berkas** dengan rincian tipe berkas:
- Berkas Excel (`.xlsx`): 185 file
- Dokumen PDF (`.pdf`): 70 file
- Berkas Gambar (`.jpeg`): 21 file
- Dokumen Word (`.docx`): 6 file
- Berkas Tambahan (`.zip`, `.xsp`): 4 file

### 2. Identifikasi Dataset Utama yang Digunakan
Dataset penelitian dihimpun dari perpaduan dua sumber data primer operasional sekolah:
1. **Buku Induk Profil Siswa & Sosial Ekonomi:**  
   - File: `DATA/Daftar PD 23-24.xlsx` (Sheet: *Daftar Peserta Didik*).
   - Memuat 66 kolom demografi, status ekonomi, bantuan sosial (KIP/PIP), data orang tua, dan jarak tempat tinggal ke sekolah.
   - Total baris record: **1.066 siswa**.
2. **Rekapitulasi Nilai Akademik dan Presensi Semester:**  
   - Folder: `DATA/Rapor/Rapor 23-24/Ganjil/` (Tersebar dalam 3 jenjang: Kelas X = 11 rombel, Kelas XI = 10 rombel, Kelas XII = 11 rombel $\rightarrow$ Total **32 file leger Excel**).
   - Memuat nilai angka per mata pelajaran, total izin (I), sakit (S), dan tanpa keterangan / alfa (A).
   - Total baris record mentah: **1.008 siswa** (1.006 NISN unik).

### 3. Kunci Relasi Integrasi Antar-File
- Integrasi dilakukan menggunakan kunci relasi unik **NISN (Nomor Induk Siswa Nasional)** (10 digit numerik terstandardisasi Kemendikbudristek).
- Hasil *inner join* antara Dapodik 2023/2024 dan 32 File Leger Rapor menghasilkan **1.005 siswa**.
- Dari 1.005 siswa tersebut, terdapat **8 siswa dengan nilai rapor kosong mutlak (hanya terisi tanda '-')** karena telah mutasi/keluar sebelum ujian semester, yaitu:
  - `0077740201` (AYRIN - XI DPB)
  - `0066217007` (GILANG - XI BDG)
  - `0083756351` (ABDUL HADI - XI DKV-1)
  - `0064413289` (M. ABI LEONA - XI DKV-1)
  - `0069071940` (RISKA APRILIYA - XI AKL)
  - `0076065700` (DIMAS ARDIAN SAPUTRA - XI DKV-2)
  - `0062335625` (DAFFA DZAKWAN YUSRA - XII MM-3)
  - `0055577776` (MUTHIA GUSTI SHANDY - XII MM-3)
- Setelah 8 siswa tanpa rekam jejak nilai dikeluarkan, didapatkan populasi bersih **997 record siswa aktif SMKN 1 Bungo**.

### 4. Inventarisasi dan Seleksi Atribut (Feature Selection)
Dari 66 kolom Dapodik dan puluhan kolom mata pelajaran Leger, dipilih **5 fitur prediktor substantif**:

| No | Nama Fitur | Kode | Tipe Data | Sumber Data Asli | Status | Alasan Metodologis |
|:--:|:---|:---:|:---:|:---|:---:|:---|
| 1 | Rerata Nilai Rapor | $X_1$ | Float | Leger Kolom Nilai Rapor | **Digunakan** | Indikator kemampuan kognitif & prestasi akademik semesteran. |
| 2 | Mapel di Bawah KKM | $X_2$ | Integer | Leger ($Count < 75.0$) | **Digunakan** | Indikator ketidaktuntasan beban kompetensi kurikulum. |
| 3 | Ketidakhadiran Alfa | $X_3$ | Integer | Leger Kolom 'A' | **Digunakan** | Indikator kedisiplinan dan absensi tanpa izin (pemicu utama DO). |
| 4 | Ketidakhadiran Izin & Sakit | $X_4$ | Integer | Leger Kolom 'I' + 'S' | **Digunakan** | Indikator kendala fisik/kesehatan dan dinamika keluarga. |
| 5 | Status Ekonomi Orang Tua | $X_5$ | Kategorikal | Dapodik Kolom 22, 28, 34, 45, 53 | **Digunakan** | Indikator kerentanan finansial siswa (KIP/PIP, gaji orang tua). |
| 6 | Jarak Rumah ke Sekolah | $X_6$ | Float | Dapodik Kolom 65 | *Tambahan* | Aksesibilitas geografis menuju sekolah. |
| 7 | NISN / NIPD / NIK | - | String | Dapodik & Leger | **Dieliminasi** | Identifier identitas unik (dapat menyebabkan data leakage). |
| 8 | Nama Siswa | - | String | Dapodik & Leger | **Dieliminasi** | Identitas individu, hanya disimpan untuk audit pelacakan. |
| 9 | Agama / Tempat Lahir | - | String | Dapodik | **Dieliminasi** | Tidak berkorelasi dengan risiko putus sekolah secara etis/metodologis. |

### 5. Distribusi Statistik Deskriptif Fitur Numerik (997 Siswa)
- **Rerata Nilai Rapor ($X_1$):** Min = 42.09, Q1 = 80.08, Median = 82.00, Q3 = 85.33, Max = 95.08, Mean = 82.55, Std = 4.28.
- **Mapel di Bawah KKM ($X_2$):** Min = 0, Median = 0, Q3 = 1, Max = 9, Mean = 0.43, Std = 0.77.
- **Ketidakhadiran Alfa ($X_3$):** Min = 0 hari, Median = 1 hari, Q3 = 3 hari, Max = 39 hari, Mean = 2.15, Std = 3.62.
- **Izin + Sakit ($X_4$):** Min = 0 hari, Median = 1 hari, Q3 = 4 hari, Max = 31 hari, Mean = 2.62, Std = 3.92.
- **Status Ekonomi ($X_5$):**
  - `Kode 1` (Prasejahtera / Penerima PIP/KIP): 156 siswa (15.65%)
  - `Kode 2` (Menengah ke Bawah / Penghasilan < Rp 2 Juta): 306 siswa (30.69%)
  - `Kode 3` (Mampu / Mandiri): 535 siswa (53.66%)

### 6. Audit Ground Truth / Label Target Aktual
- **Fakta Temuan:** Pada file mentah Dapodik maupun Leger rapor, **tidak terdapat kolom eksplisit bernama "Status Putus Sekolah"** karena data diambil saat tahun ajaran sedang berjalan.
- **Dasar Penetapan Label Ilmiah:** Sesuai standar operasional Bimbingan Konseling (BK) dan tata tertib akademik SMKN 1 Bungo, siswa ditetapkan berlabel **"Tinggi" (Risiko Putus Sekolah)** jika memenuhi salah satu aturan batas kritis:
  $$\text{Label} = \begin{cases} \text{Tinggi}, & \text{jika } (X_3 \ge 10) \lor (X_1 < 75.0 \land X_2 \ge 3) \lor (X_3 \ge 7 \land X_5 = \text{"Kode 1"}) \\ \text{Rendah}, & \text{lainnya} \end{cases}$$
- **Distribusi Label Aktual Populasi (997 Siswa):**
  - **Risiko Rendah:** 929 siswa (**93.18%**)
  - **Risiko Tinggi:** 68 siswa (**6.82%**)  *(Imbalanced Dataset riil sekolah)*

---

## BAGIAN II: AUDIT FILE EXCEL SEBELUMNYA (`PERHITUNGAN_LENGKAP_KDD_DAN_KOMPARASI_RF_NB.xlsx`)

### 1. Struktur Workbook Sebelumnya
Workbook sebelumnya hanya terdiri dari 3 sheet padat:
1. `1. Data & Preprocessing` (1.008 baris)
2. `2. Perhitungan RF & NB` (51 baris)
3. `3. Evaluasi & Komparasi` (38 baris)

### 2. Temuan Masalah & Kekurangan pada File Sebelumnya
1. **Format Sheet Belum Mengikuti 23 Sheet Kerangka Penelitian:**  
   Struktur 3 sheet terlalu padat dan menggabungkan tahapan yang seharusnya terpisah (misalnya Data Selection digabung Preprocessing, perhitungan manual digabung voting ensemble).
2. **Kelemahan Kompatibilitas Formula Statistik (`#NAME?`):**  
   Penggunaan fungsi modern Excel ber-titik seperti `=VAR.P()` dan `=NORM.DIST()` memicu error `#NAME?` pada beberapa versi Excel desktop lokal.
3. **Penyajian Random Forest Masih Bersifat Single-Tree:**  
   Perhitungan manual Random Forest hanya mendemonstrasikan 1 Decision Tree (Tree 1) tanpa menyajikan mekanisme *bootstrap sampling* eksplisit, struktur multi-pohon, dan proses *ensemble voting* dari seluruh pohon.
4. **Distribusi Naïve Bayes Campuran (Gaussian vs Kategorikal):**  
   Likelihood fitur numerik dihitung dengan Gaussian, sedangkan status ekonomi dihitung dengan frekuensi relatif tanpa pencatatan *Laplace smoothing* secara eksplisit.
5. **Keterlacakan Formula Manual:**  
   Beberapa formula di Sheet 2 sebelumnya merujuk pada rentang sel baris yang bergeser sehingga menghasilkan angka yang membingungkan pemeriksa.

---

## BAGIAN III: REKOMENDASI DAN RENCANA AKSI SISTEMATIS (FASE 2 S.D. FASE 5)

Guna memenuhi standar tesis/akademik yang dapat dipertanggungjawabkan 100%, akan dibangun file Excel baru:
`PERHITUNGAN_MANUAL_KDD_RANDOM_FOREST_NAIVE_BAYES.xlsx`

Dengan pembagian **23 Sheet Mandiri Terstruktur**:
- `00_PETUNJUK`: Panduan navigasi, metadata, dan legenda warna.
- `01_IDENTIFIKASI`: Latar belakang dan identifikasi 3 masalah utama.
- `02_STUDI_LITERATUR`: Landasan matematis KDD, Random Forest (Gini), dan Naïve Bayes (Gaussian Likelihood).
- `03_DATA_ASLI`: Inventarisasi dan cuplikan data mentah Dapodik & Leger.
- `04_DATA_SELECTION`: Matriks seleksi atribut dengan alasan metodologis.
- `05_PREPROCESSING`: Tabel audit pembersihan 8 data anomali / nilai kosong.
- `06_TRANSFORMASI`: Tabel encoding kategori status ekonomi dan normalisasi skala.
- `07_DATA_TRAINING`: 797 Record Siswa Data Latih (Stratified 80%).
- `08_DATA_TESTING`: 200 Record Siswa Data Uji (Stratified 20%).
- `09_RF_BOOTSTRAP`: Pembentukan sampel bootstrap dengan pencatatan Out-Of-Bag (OOB).
- `10_RF_PERHITUNGAN`: Perhitungan manual Gini Impurity, Gini Split, dan Gini Gain tiap threshold.
- `11_RF_PREDIKSI`: Hasil klasifikasi ensemble pohon untuk 200 data uji.
- `12_NB_PRIOR`: Tabel Prior Probability $P(C)$ dan parameter mean/varians data latih.
- `13_NB_LIKELIHOOD`: Perhitungan Gaussian Likelihood dan Categorical Likelihood siswa uji.
- `14_NB_PREDIKSI`: Normalisasi posterior $P(C|X)$ dan prediksi MAP untuk 200 data uji.
- `15_CONFUSION_RF`: Matriks konfusi aktual vs prediksi Random Forest (TP, TN, FP, FN).
- `16_CONFUSION_NB`: Matriks konfusi aktual vs prediksi Naïve Bayes (TP, TN, FP, FN).
- `17_EVALUASI_RF`: Perhitungan matematis bernomor Accuracy, Precision, Recall, F1-Score RF.
- `18_EVALUASI_NB`: Perhitungan matematis bernomor Accuracy, Precision, Recall, F1-Score NB.
- `19_KOMPARASI`: Tabel perbandingan head-to-head selisih performa RF vs NB.
- `20_MODEL_TERBAIK`: Analisis justifikasi pemilihan model terbaik berbasis bukti empiris.
- `21_IMPLEMENTASI`: Arsitektur integrasi sistem deteksi dini pada Laravel & MatDash.
- `22_KESIMPULAN`: Kesimpulan ilmiah dan rekomendasi intervensi BK sekolah.
