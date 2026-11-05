# VALIDASI METODOLOGI PENETAPAN LABEL GROUND TRUTH
**Penelitian:** Sistem Cerdas dalam Deteksi Dini Risiko Siswa Putus Sekolah dengan Komparasi Metode Random Forest dan Naïve Bayes pada Siswa SMK Negeri 1 Bungo  
**Auditor Metodologi:** Antigravity System Auditor  
**Tanggal Validasi:** 05 November 2026  

---

## 1. PERNYATAAN EKSPLISIT STATUS ATURAN LABEL
Berdasarkan audit mendalam terhadap seluruh dokumen resmi yang tersedia di direktori proyek dan folder `DATA/`, dengan ini dinyatakan secara tegas dan jujur:

> **"Aturan label merupakan asumsi operasional yang belum tervalidasi sebagai kriteria resmi BK tertulis dari SMK Negeri 1 Bungo."**

---

## 2. BUKTI AUDIT DOKUMENTASI DAN ASAL-USUL ATURAN

### A. Penelusuran Dokumen Asli Sekolah
1. **Berkas Buku Induk Dapodik (`DATA/Daftar PD 23-24.xlsx`):**  
   Memuat 66 kolom demografi, identitas, bantuan sosial, dan profil orang tua. **Tidak terdapat satu pun kolom status putus sekolah, mutasi keluar, atau label vonis risiko.**
2. **Berkas 32 Leger Rapor Ganjil (`DATA/Rapor/Rapor 23-24/Ganjil/`):**  
   Memuat daftar nilai mata pelajaran, presensi Sakit (S), Izin (I), dan Alfa (A). **Tidak ada kolom status kelulusan maupun vonis siswa putus sekolah.**
3. **Dokumen Proposal/Laporan Penelitian (`Laporan Sistem Cerdas...docx`):**  
   - Pada **BAB III (Metode Penelitian - Paragraf 635 & 639)** hanya disebutkan secara kualitatif umum:  
     *"Data yang dikumpulkan meliputi data akademik dan nonakademik siswa... pembentukan atribut target yang akan digunakan sebagai kelas dalam proses klasifikasi."*  
   - Tidak mencantumkan angka ambang batas (*threshold*) matematis spesifik untuk penentuan kelas target.
4. **Dokumen BAB IV Tesis (`BAB_IV_PERHITUNGAN_MANUAL_DAN_PEMBAHASAN_TESIS.docx`):**  
   Aturan matematis berikut:
   $$\text{Label} = \begin{cases} \text{Tinggi}, & \text{jika } (X_3 \ge 10) \lor (X_1 < 75.0 \land X_2 \ge 3) \lor (X_3 \ge 7 \land X_5 = \text{"Kode 1"}) \\ \text{Rendah}, & \text{lainnya} \end{cases}$$
   Pertama kali muncul pada teks BAB IV yang disusun pada iterasi sebelumnya sebagai **konstruksi operasional peneliti/AI** untuk menjembatani ketiadaan label riil pada data semesteran yang sedang berjalan.

---

## 3. ANALISIS PERBANDINGAN: SISWA MUTASI GENAP VS ATURAN OPERASIONAL
Untuk menguji apakah ada siswa yang *faktual* tidak melanjutkan studi di semester genap, dilakukan pencocokan NISN antara Rapor Ganjil 23-24 (1.006 NISN) dan Rapor Genap 23-24 (992 NISN).
- Ditemukan **18 siswa** yang aktif di Ganjil tetapi namanya tidak lagi tercantum di Leger Genap (berhenti/mutasi).
- Namun, model penelitian saat ini tidak menggunakan 18 siswa riil drop-out tersebut sebagai label target, melainkan menggunakan **68 siswa berlabel Tinggi** yang dibentuk dari aturan logika operasional di atas.

---

## 4. KESIMPULAN DAN STATUS AKADEMIK
Label target yang digunakan dalam pemodelan ini adalah **Label Indikasi Risiko Berbasis Aturan Heuristik (Rule-based Synthetic Ground Truth)**, bukan rekaman kejadian putus sekolah faktual di lapangan. Status ini harus diakui secara transparan dalam bab metodologi tesis agar tidak terjadi klaim keliru bahwa angka tersebut merupakan catatan resmi BK sekolah.
