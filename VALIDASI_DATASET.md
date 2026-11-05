# VALIDASI DATASET DAN REKONSILIASI JUMLAH DATA
**Penelitian:** Sistem Cerdas dalam Deteksi Dini Risiko Siswa Putus Sekolah dengan Komparasi Metode Random Forest dan Naïve Bayes pada Siswa SMK Negeri 1 Bungo  
**Auditor Metodologi:** Antigravity System Auditor  
**Tanggal Validasi:** 05 November 2026  

---

## 1. TABEL REKONSILIASI DATA AKTUAL DARI SUMBER ASLI

Berikut adalah hasil pelacakan aktual setiap langkah pemrosesan data dari folder `DATA/` menuju dataset final 997 record:

| Tahapan Pemrosesan | Jumlah Record | Penjelasan & Bukti Audit Lapangan |
|:---|---:|:---|
| **1. Buku Induk Dapodik (`Daftar PD 23-24.xlsx`)** | **1.066** | Total siswa terdaftar di sistem Dapodik sekolah TA 2023/2024. |
| **2. Rekap Rapor Ganjil (32 Berkas Leger)** | **1.008** | Akumulasi baris data siswa yang tercetak pada 32 file Leger rapor kelas X, XI, dan XII. |
| **3. Siswa Duplikat di Leger Rapor** | **-2** | Ditemukan 2 siswa tercatat di 2 file leger berbeda karena mutasi kelas di awal semester:<br>• `0083461451` (TRI ANDIKA DESTU LAPANO): di X AK (baris 38 - nilai kosong) dan di X BDG-1 (baris 41 - nilai lengkap).<br>• `0072278358` (ANNISA NUR HAPSARI): di XI DKV-3 (baris 15 - nilai lengkap) dan di XII MM-3 (baris 14 - nilai kosong). |
| **4. Leger Bersih (NISN Unik)** | **1.006** | Jumlah individu riil siswa yang memiliki lembar leger penilaian semester ganjil. |
| **5. Siswa di Dapodik tetapi TIDAK ADA di Leger** | **61** | Siswa yang tercatat di Buku Induk Dapodik tetapi tidak mengikuti pembelajaran/ujian di semester ganjil 2023/2024. |
| **6. Siswa di Leger tetapi TIDAK ADA di Dapodik 23-24** | **1** | Siswa `0066415551` (CITRA BUNGA LESTARI - XI AKL). Siswa ini tercatat di Leger Rapor Ganjil dan baru masuk di Buku Induk Dapodik TA 2024/2025. |
| **7. Hasil Inner Join (Dapodik 23-24 ∩ Leger)** | **1.005** | Jumlah irisan identitas siswa yang terverifikasi di kedua sumber data primer. |
| **8. Siswa dengan Nilai Kosong Mutlak Dikeluarkan** | **-9** | **9 siswa** dikeluarkan karena seluruh kolom nilai mata pelajarannya hanya berisi karakter `"-"` (tidak memiliki data kognitif akademik sama sekali):<br>1. `0077740201` (AYRIN - XI DPB)<br>2. `0066217007` (GILANG - XI BDG)<br>3. `0083756351` (ABDUL HADI - XI DKV-1)<br>4. `0064413289` (M. ABI LEONA - XI DKV-1)<br>5. `0069071940` (RISKA APRILIYA - XI AKL)<br>6. `0076065700` (DIMAS ARDIAN SAPUTRA - XI DKV-2)<br>7. `0062335625` (DAFFA DZAKWAN YUSRA - XII MM-3)<br>8. `0055577776` (MUTHIA GUSTI SHANDY - XII MM-3)<br>9. `3048569587` (NATASYA ERAWATI - XII MM-3) |
| **9. Ditambahkan Siswa Leger Valid (Citra Bunga Lestari)** | **+1** | Siswa `0066415551` memiliki nilai lengkap di Leger dan profil Dapodik di 2024/2025, sehingga dimasukkan ke dataset bersih. |
| **DATASET FINAL PENELITIAN** | **997** | **997 Record Siswa Bersih** dengan atribut akademik dan demografi lengkap 100%. |

---

## 2. AUDIT KHUSUS ATRIBUT $X_1$ SAMPAI $X_6$

| Fitur | Nama Kolom Asli di Sumber Data | Tipe Data | Record Valid | Record Kosong | Min | Max | Kategori / Transformasi |
|:---:|:---|:---:|:---:|:---:|:---:|:---:|:---|
| **$X_1$** | Kolom Nilai Mata Pelajaran Leger Rapor | Float | 997 | 0 | 42.09 | 95.08 | Rerata aritmatika nilai rapor semester ganjil. |
| **$X_2$** | Nilai Leger < 75.0 (Ambang KKM) | Integer | 997 | 0 | 0 | 9 | Akumulasi jumlah mata pelajaran di bawah KKM. |
| **$X_3$** | Header Leger: "A" (Kolom 19) | Integer | 997 | 0 | 0 | 39 | Akumulasi hari tanpa keterangan resmi (Alfa). |
| **$X_4$** | Header Leger: "I" (Kolom 18) + "S" (Kolom 17) | Integer | 997 | 0 | 0 | 31 | Penjumlahan izin dan sakit bersurat. |
| **$X_5$** | Dapodik Kolom 22, 28, 34, 45, 53 | Kategori | 997 | 0 | - | - | **Ordinal Encoding:**<br>• Kode 1: Prasejahtera / PIP (156)<br>• Kode 2: Menengah ke Bawah (306)<br>• Kode 3: Mandiri / Mampu (535) |
| **$X_6$** | Dapodik Kolom 65: "Jarak Rumah ke Sekolah (KM)" | Float | 997 | 0 | 0.0 | 500.0 | Jarak tempuh tempat tinggal siswa ke SMKN 1 Bungo. |

### Audit Anomali Nilai Jarak $X_6 = 500\text{ KM}$:
- **Temuan Rekord:** Siswa atas nama **ARDILA JUNIRA NOVYAN** (NISN `3072302726`, Baris 353) memiliki nilai jarak **500 KM**.
- **Analisis Alamat Asli di Dapodik:** Alamat siswa tertulis *"JL. TAMAN SISWA NO. 74 MUARA BUNGO, KELURAHAN MANGGIS, KEC. BATHIN III"*. Kelurahan Manggis berada di dalam kota Muara Bungo yang jarak aslinya ke SMKN 1 Bungo hanya sekitar **500 Meter (0.5 KM)**.
- **Penyebab:** Kesalahan input operator sekolah (*human error / typo*), di mana operator mengetikkan angka `500` dengan maksud 500 meter, tetapi satuan kolom di Dapodik adalah Kilometer (KM).
- **Rekomendasi Metodologis:** Tetap mempertahankan nilai asli 500.0 sesuai integritas data mentah Dapodik, namun mencatatnya secara transparan di catatan kaki laporan audit.
