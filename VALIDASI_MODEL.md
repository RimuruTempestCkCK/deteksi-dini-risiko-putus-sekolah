# VALIDASI MODEL DAN ALGORITMA AKTUAL
**Penelitian:** Sistem Cerdas dalam Deteksi Dini Risiko Siswa Putus Sekolah dengan Komparasi Metode Random Forest dan Naïve Bayes pada Siswa SMK Negeri 1 Bungo  
**Auditor Metodologi:** Antigravity System Auditor  
**Tanggal Validasi:** 05 November 2026  

---

## 1. VALIDASI PEMBAGIAN DATA (DATA SPLITTING)
- **Metode Pembagian:** **Stratified Train-Test Split**
- **Ukuran Partisi:** 
  - **Data Latih (80%):** 797 record
  - **Data Uji (20%):** 200 record
- **Random Seed / State:** `42`
- **Distribusi Label Sebelum & Sesudah Split:**
  - **Populasi Awal (997 Siswa):** 929 Rendah (93.18%) dan 68 Tinggi (6.82%).
  - **Data Latih (797 Siswa):** 743 Rendah (93.22%) dan 54 Tinggi (6.78%).
  - **Data Uji (200 Siswa):** 186 Rendah (93.00%) dan 14 Tinggi (7.00%).
- **Jaminan Integritas:** Dilakukan verifikasi irisan himpunan index baris ($\text{Train} \cap \text{Test} = \emptyset$). Tidak ada satupun record yang muncul ganda di kedua partisi.

---

## 2. SPESIFIKASI DAN PARAMETER RANDOM FOREST AKTUAL
- **Pustaka / Library:** `scikit-learn` v1.9.1 (Python 3.14)
- **Modul:** `sklearn.ensemble.RandomForestClassifier`
- **Parameter Aktual yang Digunakan:**
  - `n_estimators`: **100** (Membentuk 100 pohon keputusan independen)
  - `criterion`: `'gini'` (Menggunakan Gini Impurity untuk pemilihan simpul split)
  - `max_depth`: **5** (Pembatasan kedalaman untuk mencegah overfitting)
  - `min_samples_split`: **2**
  - `min_samples_leaf`: **1**
  - `max_features`: `'sqrt'` ($\sqrt{6} \approx 2.45 \rightarrow$ memilih 2 fitur acak di setiap kandidat split)
  - `bootstrap`: `True` (Pengambilan sampel dengan pengembalian)
  - `random_state`: **42**
- **Mekanisme Prediksi:** Agregasi suara terbanyak (*Majority Voting*) dari ke-100 pohon keputusan.
- **Keterbatasan Penyajian Excel:** Pada lembar Excel, disajikan **perhitungan manual 1 Pohon Representatif (Tree 1)** untuk mendemonstrasikan rumus matematis Gini Impurity, Gini Split, dan Gini Gain, sedangkan pada tabel hasil prediksi akhir dicantumkan akumulasi voting dari seluruh 100 pohon.

---

## 3. SPESIFIKASI DAN PARAMETER NAÏVE BAYES AKTUAL
- **Pustaka / Library:** `scikit-learn` v1.9.1
- **Modul:** `sklearn.naive_bayes.GaussianNB`
- **Parameter Aktual yang Digunakan:**
  - `var_smoothing`: `1e-9` (Nilai epsilon stabilisasi untuk mencegah pembagian dengan nol pada varians yang sangat kecil)
  - `priors`: `None` (Dihitung otomatis dari frekuensi prior data latih: $P(\text{Rendah}) = 743/797 \approx 0.9322$, $P(\text{Tinggi}) = 54/797 \approx 0.0678$)
- **Catatan Metodologis Distribusi Fitur:**
  Pada implementasi Python bawaan `GaussianNB`, seluruh fitur ($X_1$ s.d. $X_6$) diasumsikan mengikuti distribusi kontinu Gaussian (Normal). Namun pada pengujian manual di lembar Excel, fitur kategorikal status ekonomi ($X_5$) dapat dihitung menggunakan frekuensi bersyarat kategorikal dengan smoothing.
