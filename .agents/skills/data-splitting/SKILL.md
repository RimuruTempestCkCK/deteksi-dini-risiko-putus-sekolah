---
name: data-splitting
description: >-
  Metodologi pembagian data latih (training data) dan data uji (testing data) menggunakan Stratified Sampling
  atau K-Fold Cross Validation untuk mempertahankan proporsi kelas risiko putus sekolah. Gunakan skill ini sebelum melatih model.
---

# Skill: Pembagian Data Training dan Testing

Skill ini mengatur tata cara pembagian dataset menjadi data latih (*training set*) dan data uji (*testing set*) dengan pendekatan yang objektif, representatif, dan mencegah *data leakage*.

## 1. Rasio Pembagian Dataset
- **Rasio Standar**: 80% Data Training : 20% Data Testing (atau 70:30 tergantung ukuran sampel dan kebutuhan evaluasi).
- **Random Seed**: Tetapkan seed konstan (misal: `seed = 42`) demi reproduksibilitas hasil eksperimen ilmiah.

## 2. Metode Stratified Sampling
Karena kasus siswa berisiko putus sekolah umumnya memiliki ketimpangan kelas (*imbalanced class*), pembagian data **WAJIB** menerapkan **Stratified Sampling**:
- Memastikan proporsi label `Risiko Tinggi` dan `Risiko Rendah` di data latih persis sebanding dengan data uji.
- Menghindari skenario di mana seluruh sampel berisiko tinggi terkonsentrasi di data latih sehingga data uji tidak merepresentasikan kasus nyata.

## 3. Validasi K-Fold Cross-Validation
- Terapkan **5-Fold** atau **10-Fold Stratified Cross Validation** saat fase tuning *hyperparameter* untuk membuktikan model bebas dari *overfitting* dan *underfitting*.

## 4. Checklist Prosedur
- [ ] Pisahkan fitur prediktor ($X$) dan variabel target ($y$).
- [ ] Terapkan `train_test_split(X, y, test_size=0.2, stratify=y, random_state=42)`.
- [ ] Verifikasi jumlah baris dan rasio kelas pada data training dan testing.
