# CATATAN METODOLOGI PENELITIAN
**Judul Penelitian:** Sistem Cerdas dalam Deteksi Dini Risiko Siswa Putus Sekolah dengan Komparasi Metode Random Forest dan Naïve Bayes pada Siswa SMK Negeri 1 Bungo  

---

## 1. DESAIN EKSPERIMEN & ALUR KDD
Eksperimen machine learning dirancang secara ketat mengikuti 5 tahapan Knowledge Discovery in Databases (KDD):
1. **Data Selection:** Menghubungkan Dapodik TA 2023/2024 dan 32 File Leger Nilai Semester Ganjil TA 2023/2024 via NISN.
2. **Data Preprocessing:** Mengeliminasi 8 siswa yang nilainya kosong mutlak (hanya terisi '-') akibat mutasi/keluar sebelum semester ganjil berakhir. Menghasilkan total 997 record siswa bersih dan valid.
3. **Data Transformation:**
   - Mentransformasikan nilai numerik: $X_1$ (Rerata Nilai Rapor), $X_2$ (Jumlah Mapel di Bawah KKM), $X_3$ (Ketidakhadiran Alfa), $X_4$ (Ketidakhadiran Izin & Sakit), $X_6$ (Jarak Rumah ke Sekolah dalam KM).
   - Melakukan *ordinal encoding* status ekonomi keluarga $X_5$: Kode 1 = Prasejahtera (1), Kode 2 = Menengah ke Bawah (2), Kode 3 = Mampu (3).
4. **Data Splitting (Pembagian Data):**
   - Menggunakan **Stratified Sampling** dengan rasio **80% Data Latih (797 siswa)** dan **20% Data Uji (200 siswa)**.
   - Menggunakan **Random Seed / State = 42** untuk reproduktibilitas 100%.
   - Stratifikasi menjamin distribusi kelas minoritas tetap konsisten:
     - Data Latih: 743 Risiko Rendah (93.22%), 54 Risiko Tinggi (6.78%).
     - Data Uji: 186 Risiko Rendah (93.00%), 14 Risiko Tinggi (7.00%).
5. **Data Mining (Pelatihan Model):**
   - **Random Forest:** `n_estimators = 100`, `max_depth = 5`, `criterion = 'gini'`, `random_state = 42`.
   - **Naïve Bayes:** `GaussianNB(var_smoothing=1e-9)`.
6. **Evaluation:** Menguji kedua model terhadap 200 data uji independen yang sama persis tanpa *data leakage*.

---

## 2. HASIL VERIFIKASI METRIK EVALUASI EMPIRIS AKTUAL

### A. Random Forest Classifier
- **Confusion Matrix:**
  - True Positive ($TP$) = 12
  - True Negative ($TN$) = 186
  - False Positive ($FP$) = 0
  - False Negative ($FN$) = 2
- **Metrik Performa:**
  - $\text{Accuracy} = \frac{186 + 12}{200} = \mathbf{99.00\%}$
  - $\text{Precision} = \frac{12}{12 + 0} = \mathbf{100.00\%}$
  - $\text{Recall} = \frac{12}{12 + 2} = \mathbf{85.71\%}$
  - $\text{F1-Score} = \frac{2 \times 1.0000 \times 0.8571}{1.0000 + 0.8571} = \mathbf{92.31\%}$

### B. Naïve Bayes Classifier (GaussianNB)
- **Confusion Matrix:**
  - True Positive ($TP$) = 13
  - True Negative ($TN$) = 170
  - False Positive ($FP$) = 16
  - False Negative ($FN$) = 1
- **Metrik Performa:**
  - $\text{Accuracy} = \frac{170 + 13}{200} = \mathbf{91.50\%}$
  - $\text{Precision} = \frac{13}{13 + 16} = \mathbf{44.83\%}$
  - $\text{Recall} = \frac{13}{13 + 1} = \mathbf{92.86\%}$
  - $\text{F1-Score} = \frac{2 \times 0.4483 \times 0.9286}{0.4483 + 0.9286} = \mathbf{60.47\%}$

---

## 3. ANALISIS KOMPARASI DAN JUSTIFIKASI MODEL TERBAIK
1. **Analisis False Positive (Sensitivitas vs Kesalahan Vonis):**
   Naïve Bayes menghasilkan **16 False Positive**, artinya ada 16 siswa non-risiko yang salah divonis berisiko tinggi. Hal ini disebabkan asumsi independensi fitur (*conditional independence*) pada Naïve Bayes yang mengabaikan korelasi alami antara jumlah alfa dan penurunan nilai.
2. **Superioritas Random Forest:**
   Random Forest berhasil mereduksi False Positive menjadi **0 (Presisi 100.00%)** melalui agregasi 100 pohon keputusan independen (*bootstrap aggregation*), sehingga kebijakan intervensi bimbingan konseling dan alokasi bantuan PIP di SMKN 1 Bungo benar-benar tepat sasaran.
3. **Kesimpulan Pemilihan Model:**
   Algoritma **Random Forest terpilih secara mutlak sebagai model terbaik** untuk diimplementasikan ke dalam sistem deteksi dini sekolah.
