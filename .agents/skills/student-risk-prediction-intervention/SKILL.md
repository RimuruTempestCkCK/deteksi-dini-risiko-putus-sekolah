---
name: student-risk-prediction-intervention
description: >-
  Prosedur interpretasi hasil prediksi risiko siswa per individu, analisis faktor pemicu (fitur dominan),
  serta formulasi rekomendasi intervensi edukatif dan sosio-ekonomi bagi pihak sekolah/guru BK.
---

# Skill: Hasil Prediksi Risiko Siswa & Rekomendasi Intervensi

Skill ini memandu penafsiran hasil luaran prediksi siswa (*Hasil Prediksi Risiko Siswa*) dan penyusunan strategi intervensi terarah (*targeted intervention*) di SMK Negeri 1 Bungo.

## 1. Klasifikasi Tingkat Risiko Siswa
Luaran prediksi dikelompokkan ke dalam kategori aksi:
- **Risiko Tinggi (High Risk)**:
  - Skor probabilitas $\ge 0.70$
  - Karakteristik umum: Akumulasi ketidakhadiran tanpa keterangan (Alpha) tinggi, nilai di bawah KKM pada beberapa mapel kunci, status ekonomi prasejahtera tanpa KIP.
  - Prioritas intervensi: **Mendesak (Immediate Action)**.
- **Risiko Sedang / Waspada (Medium Risk)**:
  - Skor probabilitas $0.40 - 0.69$
  - Karakteristik umum: Fluktuasi nilai akademik atau tren penurunan kehadiran dalam 1 bulan terakhir.
  - Prioritas intervensi: **Monitoring Berkala**.
- **Risiko Rendah (Low Risk / Aman)**:
  - Skor probabilitas $< 0.40$
  - Karakteristik umum: Kehadiran stabil $> 90\%$, prestasi akademik konsisten, dukungan keluarga memadai.

## 2. Matriks Tindakan Intervensi Sekolah
| Faktor Pemicu Utama | Rekomendasi Intervensi Guru BK / Wali Kelas |
| :--- | :--- |
| **Faktor Akademik** (Nilai rendah / tidak tuntas) | Remedial terjadwal, bimbingan tutor sebaya, penugasan portofolio terstruktur. |
| **Faktor Kehadiran** (Tinggi Alpha) | Konseling individual, pemanggilan orang tua/wali, *home visit* (kunjungan ke rumah). |
| **Faktor Ekonomi** (Penghasilan rendah, tidak ada KIP) | Usulan prioritas bantuan beasiswa KIP/PIP baru, subsidi peralatan praktik kejuruan, keringanan iuran. |
| **Faktor Lingkungan & Jarak** | Pemetaan transportasi, fasilitas asrama sekolah atau kos binaan bagi yang tinggal jauh. |

## 3. Dokumentasi Hasil & Pelaporan
1. Ekspor rekapitulasi siswa berisiko tinggi secara berkala per semester.
2. Catat riwayat tindak lanjut bimbingan konseling pada modul intervensi sistem web.
