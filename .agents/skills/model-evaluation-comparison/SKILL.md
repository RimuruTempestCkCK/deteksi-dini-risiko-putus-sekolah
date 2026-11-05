---
name: model-evaluation-comparison
description: >-
  Perhitungan Confusion Matrix, metrik evaluasi Accuracy, Precision, Recall, F1-Score,
  serta komparasi performa antara Random Forest vs Naive Bayes dengan pengujian signifikansi ilmiah.
---

# Skill: Evaluasi dan Komparasi Model (Random Forest vs Naive Bayes)

Skill ini memandu proses evaluasi kinerja prediktif kedua algoritma menggunakan metrik standar data mining serta interpretasi hasil komparasi untuk penentuan rekomendasi terbaik.

## 1. Penyusunan Confusion Matrix
Tabel matriks evaluasi biner:
| | Prediksi Positif (Risiko Tinggi) | Prediksi Negatif (Risiko Rendah) |
| :--- | :--- | :--- |
| **Aktual Positif** | True Positive (TP) | False Negative (FN) |
| **Aktual Negatif** | False Positive (FP) | True Negative (TN) |

> **Catatan Kritis**: Dalam konteks deteksi risiko putus sekolah, **False Negative (FN)** (siswa sebenarnya berisiko namun tidak terdeteksi) berdampak fatal karena siswa kehilangan kesempatan memperoleh bimbingan/bantuan, sehingga **Recall** menjadi metrik prioritas utama selain Akurasi.

## 2. Rumus Metrik Evaluasi
1. **Accuracy (Akurasi)**:
   $$\text{Accuracy} = \frac{TP + TN}{TP + TN + FP + FN}$$
2. **Precision (Presisi)**:
   $$\text{Precision} = \frac{TP}{TP + FP}$$
3. **Recall / Sensitivity**:
   $$\text{Recall} = \frac{TP}{TP + FN}$$
4. **F1-Score**:
   $$\text{F1-Score} = 2 \times \frac{\text{Precision} \times \text{Recall}}{\text{Precision} + \text{Recall}}$$

## 3. Komparasi dan Dasar Ilmiah Rekomendasi
1. Sajikan tabel komparasi berdampingan:
   - Random Forest: Akurasi, Presisi, Recall, F1-Score, Waktu Komputasi.
   - Naive Bayes: Akurasi, Presisi, Recall, F1-Score, Waktu Komputasi.
2. Analisis keunggulan:
   - Random Forest unggul dalam menangani korelasi non-linear, dataset kompleks, dan memiliki ketahanan tinggi terhadap overfitting berkat agregasi ensemble.
   - Naive Bayes unggul dalam kecepatan inferensi dan kesederhanaan matematis.
