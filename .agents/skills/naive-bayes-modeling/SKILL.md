---
name: naive-bayes-modeling
description: >-
  Implementasi, pelatihan, estimasi probabilitas prior dan likelihood Gaussian/Categorical,
  serta Laplace smoothing untuk algoritma Naive Bayes dalam deteksi dini risiko putus sekolah.
---

# Skill: Pemodelan Naive Bayes

Skill ini mengatur formulasi dan implementasi algoritma probabilistik **Naive Bayes** untuk klasifikasi risiko putus sekolah siswa SMKN 1 Bungo.

## 1. Landasan Teorema Bayes
Algoritma mengasumsikan independensi bersyarat antar fitur (*conditional independence*) berdasarkan kelas target $y$:
$$P(y \mid X) = \frac{P(y) \prod_{j=1}^{d} P(x_j \mid y)}{P(X)}$$

Di mana:
- $P(y)$: Probabilitas prior kelas $y$ (Rasio frekuensi kelas pada data training).
- $P(x_j \mid y)$: Likelihood fitur ke-$j$ pada kelas $y$.
- $\hat{y}$: Kelas dengan nilai probabilitas posterior maksimum:
  $$\hat{y} = \arg\max_{y \in \{0, 1\}} \left( P(y) \prod_{j=1}^{d} P(x_j \mid y) \right)$$

## 2. Penanganan Atribut Campuran
1. **Gaussian Naive Bayes (Fitur Kontinyu/Numerik)**:
   Digunakan untuk nilai akademik rata-rata dan total alpha:
   $$P(x_j \mid y) = \frac{1}{\sqrt{2\pi \sigma_y^2}} \exp\left( - \frac{(x_j - \mu_y)^2}{2\sigma_y^2} \right)$$
2. **Categorical / Multinomial dengan Laplace Smoothing**:
   Digunakan untuk fitur kategorik (KIP/PIP, Pekerjaan Ortu) dengan koreksi Laplace ($\alpha = 1$) guna menghindari probabilitas nol ($0$ frequency problem):
   $$P(x_j = k \mid y) = \frac{N_{y,k} + \alpha}{N_y + \alpha K}$$

## 3. Log-Likelihood Transformation
Untuk mencegah masalah *underflow* akibat perkalian probabilitas kecil yang berulang, gunakan penjumlahan log-probabilitas:
$$\log P(y \mid X) \propto \log P(y) + \sum_{j=1}^{d} \log P(x_j \mid y)$$
