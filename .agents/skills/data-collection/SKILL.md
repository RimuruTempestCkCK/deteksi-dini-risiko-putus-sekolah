---
name: data-collection
description: >-
  Panduan dan prosedur pengumpulan data siswa SMKN 1 Bungo dari file Dapodik dan Leger nilai semester (X, XI, XII).
  Gunakan skill ini saat mengumpulkan, mengidentifikasi, mengekstrak, atau memverifikasi sumber data mentah.
---

# Skill: Pengumpulan Data Siswa (SMKN 1 Bungo)

Skill ini mengatur tata cara dan standar operasional dalam pengumpulan serta inventarisasi data mentah siswa SMK Negeri 1 Bungo untuk keperluan deteksi dini risiko putus sekolah.

## 1. Sumber Data Mentah
Data bersumber dari dua pilar utama di SMKN 1 Bungo:
1. **Buku Induk / Profil Dapodik**:
   - `DATA/Daftar PD 23-24.xlsx`
   - `DATA/PD 2024-2025.xlsx`
   - `DATA/PD 2025-2026.xlsx`
   - Berisi informasi demografi siswa: NISN, Nama, Jenis Kelamin, Tempat/Tanggal Lahir, Agama, Alamat, Status Tempat Tinggal, Penerima KIP/PIP/KKS, Data Orang Tua/Wali (Pekerjaan Ayah/Ibu, Penghasilan Ayah/Ibu).
2. **Leger Nilai Akhir Mata Pelajaran & Absensi**:
   - Subfolder `DATA/2023-2024/`, `DATA/2024-2025/` (Semester Ganjil & Genap untuk jenjang X, XI, XII dan jurusan AKL, BDG, DKV, DPB, MP, TKJ).
   - Berisi rekapitulasi nilai per mata pelajaran normatif, adaptif, kejuruan, serta data absensi (Sakit, Izin, Alpha/Tanpa Keterangan).

## 2. Prosedur Verifikasi Integritas Data
1. Periksa kelengkapan file Excel per tahun ajaran dan semester.
2. Pastikan file tidak *corrupt* dan header kolom sesuai struktur Dapodik/Leger standar.
3. Konfirmasi konsistensi pengenal unik: gunakan **NISN** sebagai kunci relasi antar-tabel (*primary key* relasional).

## 3. Checklist Pengumpulan
- [ ] Profil demografi siswa lengkap beserta status bantuan (KIP/PIP).
- [ ] Data pekerjaan dan rata-rata penghasilan orang tua berhasil diekstrak.
- [ ] Nilai akademik semesteran lengkap dan terkonversi dalam skala 0–100.
- [ ] Rekap absensi (terutama jumlah ketidakhadiran tanpa keterangan / Alpha) tercatat.
