# VALIDASI MASALAH TARGET LEAKAGE KONSEPTUAL
**Penelitian:** Sistem Cerdas dalam Deteksi Dini Risiko Siswa Putus Sekolah dengan Komparasi Metode Random Forest dan Naïve Bayes pada Siswa SMK Negeri 1 Bungo  
**Auditor Metodologi:** Antigravity System Auditor  
**Tanggal Validasi:** 05 November 2026  

---

## 1. TEMUAN HUBUNGAN FITUR INPUT VS LABEL TARGET

Pada penelitian ini, label target dibentuk dengan aturan deterministik:
$$\text{Label} = f(X_1, X_2, X_3, X_5)$$
di mana:
- $X_1$ = Rerata Nilai Rapor
- $X_2$ = Jumlah Mapel di Bawah KKM
- $X_3$ = Ketidakhadiran Alfa
- $X_5$ = Status Ekonomi Orang Tua

Sedangkan variabel input yang dimasukkan ke dalam algoritma Random Forest dan Naïve Bayes adalah:
$$\mathbf{X} = [X_1, X_2, X_3, X_4, X_5, X_6]$$

---

## 2. KONSEKUENSI METODOLOGIS DAN RISIKO TARGET LEAKAGE

1. **Model Mempelajari Aturan Deterministik (Bukan Pola Alami):**  
   Karena target didefinisikan secara mekanis dari kombinasi $X_1, X_2, X_3, X_5$, model *machine learning* pada dasarnya hanya dilatih untuk "merekonstruksi kembali aturan IF-THEN" yang telah ditanamkan ke dalam label.
2. **Feature Importance Dominan pada Alfa ($X_3$):**  
   Berdasarkan ekstraksi *feature importance* aktual Random Forest:
   - **Alfa ($X_3$): 66.42%**
   - **Rerata Nilai ($X_1$): 12.59%**
   - **Mapel di Bawah KKM ($X_2$): 7.15%**
   - **Izin & Sakit ($X_4$): 7.01%**
   - **Status Ekonomi ($X_5$): 5.03%**
   - **Jarak ($X_6$): 1.79%**  
   Hal ini membuktikan bahwa algoritma secara cerdas menangkap bahwa ambang batas $\text{Alfa} \ge 10$ dan $\text{Alfa} \ge 7$ adalah prediktor dominan yang menentukan label target.
3. **Makna Akurasi 99.00% pada Random Forest:**  
   - Akurasi 99.00% dan Presisi 100.00% **hanya membuktikan kemampuan Random Forest dalam meniru formula/aturan logika operasional** dengan ketelitian sangat tinggi.
   - Angka 99.00% tersebut **TIDAK OTOMATIS MEMBUKTIKAN** bahwa model mampu memprediksi siswa yang di dunia nyata 100% pasti akan putus sekolah.
4. **Alasan Naïve Bayes Memiliki Presisi Rendah (44.83%):**  
   Naïve Bayes berasumsi bahwa setiap fitur independen bersyarat ($P(X_1, X_3|C) = P(X_1|C) \times P(X_3|C)$). Karena pada kenyataannya aturan pembentukan label menggabungkan kondisi logis AND (`X1 < 75 AND X2 >= 3` serta `X3 >= 7 AND X5 = Kode 1`), asumsi independensi Naïve Bayes runtuh, sehingga Naïve Bayes menghasilkan **16 False Positive** (presisi anjlok ke 44.83%).

---

## 3. IMPLIKASI PADA PENULISAN TESIS DAN EXCEL
- Masalah *target leakage* konseptual ini **tidak boleh disembunyikan**.
- Di dalam laporan dan sheet Excel, harus dicantumkan penjelasan bahwa model ini berfungsi sebagai **"Sistem Inferensi Pendukung Keputusan Otomatis Terhadap Standar Indikator Risiko Sekolah"**, bukan model peramal masa depan murni.
