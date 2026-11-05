---
name: random-forest-modeling
description: >-
  Implementasi, pelatihan, tuning parameter (n_estimators, max_depth, criterion Gini/Entropy),
  serta ekstraksi feature importance dari algoritma Random Forest untuk deteksi dini risiko putus sekolah.
---

# Skill: Pemodelan Random Forest

Skill ini mengatur implementasi algoritma ensemble **Random Forest Classifier** dalam memprediksi risiko putus sekolah siswa SMKN 1 Bungo.

## 1. Konsep Matematis & Algoritma
Random Forest membangun himpunan pohon keputusan (*ensemble of Decision Trees*) menggunakan teknik **Bagging (Bootstrap Aggregating)** dan **Random Feature Selection**:
1. **Bootstrapping**: Mengambil sampel acak dengan pengembalian (*with replacement*) sebanyak $N$ dari data training.
2. **Splitting Criteria**: Mengukur kemurnian simpul (*node purity*) menggunakan **Gini Impurity** atau **Information Gain (Entropy)**:
   $$\text{Gini}(D) = 1 - \sum_{i=1}^{C} p_i^2$$
3. **Agregasi / Majority Voting**: Kelas prediksi akhir ditentukan oleh mayoritas suara dari seluruh pohon ($B$ trees):
   $$\hat{y} = \text{mode} \{ T_1(x), T_2(x), \dots, T_B(x) \}$$

## 2. Hyperparameter Tuning
Parameter utama yang diatur dalam eksperimen:
- `n_estimators`: Jumlah pohon (rentang pengujian: 50, 100, 200).
- `max_depth`: Kedalaman maksimum pohon (mencegah overfitting, misal: 5, 10, None).
- `min_samples_split`: Jumlah sampel minimum yang dibutuhkan untuk membagi simpul internal (2, 5).
- `min_samples_leaf`: Jumlah sampel minimum pada leaf node (1, 2).

## 3. Analisis Feature Importance
- Hitung kontribusi relatif setiap atribut (Mean Decrease in Impurity / Gini Importance).
- Analisis fitur apa yang paling dominan mempengaruhi risiko (misal: jumlah Alpha, rata-rata nilai, status ekonomi).
