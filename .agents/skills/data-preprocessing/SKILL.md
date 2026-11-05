---
name: data-preprocessing
description: >-
  Prosedur pembersihan data, penanganan missing values, encoding variabel kategori, normalisasi/standardisasi fitur,
  dan seleksi atribut untuk deteksi dini risiko putus sekolah. Gunakan skill ini pada tahap persiapan data sebelum pemodelan.
---

# Skill: Preprocessing Data Siswa

Skill ini memandu standarisasi pembersihan, imputasi, rekayasa fitur (*feature engineering*), dan transformasi data siswa SMKN 1 Bungo agar siap digunakan oleh algoritma Machine Learning (Random Forest & Naive Bayes).

## 1. Penanganan Missing Values & Inkonsistensi Data
1. **Atribut Kategorikal**:
   - Status KIP/PIP: Ubah nilai kosong (`null`/`empty`) menjadi `0` (Tidak Menerima).
   - Pekerjaan Orang Tua: Nilai hilang diimputasi dengan modus kategori (misal: "Lainnya" atau "Tidak Bekerja").
2. **Atribut Numerik**:
   - Penghasilan Orang Tua: Dikonversi ke interval nominal atau ordinal (1 = < 1 Juta, 2 = 1-2 Juta, dst.).
   - Nilai Akademik: Imputasi nilai kosong menggunakan rata-rata (*mean*) kelas/jurusan untuk mata pelajaran terkait.
   - Absensi (S/I/A): Nilai kosong diasumsikan 0 hari tidak hadir.

## 2. Feature Engineering & Seleksi Atribut
Fitur-fitur utama yang ditransformasi untuk input model:
| Variabel | Tipe Data | Deskripsi / Penanganan |
| :--- | :--- | :--- |
| `rata_rata_nilai` | Numerik | Rata-rata nilai rapor seluruh mata pelajaran |
| `kehadiran_persen` | Numerik | Persentase kehadiran = $((HariAktif - Alpha - S - I) / HariAktif) \times 100\%$ |
| `total_alpha` | Numerik | Total ketidakhadiran tanpa keterangan |
| `status_ekonomi` | Ordinal | Bobot gabungan penghasilan orang tua & kepemilikan KIP |
| `jarak_ke_sekolah` | Numerik | Jarak tempuh tempat tinggal ke SMKN 1 Bungo (km) |
| `status_risiko` | Target | Label biner / multi-kelas: **Rendah (0)** vs **Tinggi (1)** |

## 3. Encoding & Scaling
- **Categorical Encoding**: Label Encoding atau One-Hot Encoding untuk jurusan dan jenis kelamin.
- **Normalisasi**: Min-Max Scaler atau Standard Scaler untuk atribut numerik jika dibutuhkan oleh model probabilitas kontinyu (Gaussian Naive Bayes).
