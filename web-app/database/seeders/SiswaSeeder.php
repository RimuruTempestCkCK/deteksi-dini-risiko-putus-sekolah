<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\RaporSemester;
use App\Models\FiturDataset;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        $kelasList = Kelas::all();
        $kelasXAk = Kelas::where('nama_kelas', 'X AK')->first() ?? $kelasList->first();
        $kelasBdg = Kelas::where('nama_kelas', 'X BDG-1')->first() ?? $kelasList->first();
        $kelasDkv = Kelas::where('nama_kelas', 'X DKV-1')->first() ?? $kelasList->first();

        // 1. Data Siswa Spesifik (Representasi Dataset Riil SMKN 1 Bungo)
        $specificSiswa = [
            [
                'kelas_id' => $kelasXAk->id,
                'nisn' => '0091577109',
                'nipd' => '23241001',
                'nama_lengkap' => 'Ahmad Nabil',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Muara Bungo',
                'tanggal_lahir' => '2008-05-12',
                'alamat' => 'Jl. Lintas Sumatera Km 3, Bathin III',
                'alat_transportasi' => 'Sepeda Motor',
                'is_penerima_kps' => false,
                'is_penerima_kip' => false,
                'is_penerima_kks' => false,
                'is_layak_pip' => false,
                'pekerjaan_ayah' => 'Wiraswasta',
                'pekerjaan_ibu' => 'Ibu Rumah Tangga',
                'penghasilan_ayah' => 2500000,
                'penghasilan_ibu' => 0,
                'jml_saudara_kandung' => 2,
                'jarak_rumah_km' => 3.5,
                'status_keaktifan' => 'Aktif',
                // Data Akademik & Presensi
                'rerata_nilai' => 82.5,
                'mapel_kkm' => 0,
                'alfa' => 1,
                'izin' => 2,
                'sakit' => 1,
                'target' => 'Rendah',
            ],
            [
                'kelas_id' => $kelasXAk->id,
                'nisn' => '0086426000',
                'nipd' => '23241002',
                'nama_lengkap' => 'Allini Toher Umi Jaya',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Bungo',
                'tanggal_lahir' => '2007-11-20',
                'alamat' => 'Dusun Sungai Mengkuang',
                'alat_transportasi' => 'Angkutan Umum',
                'is_penerima_kps' => true,
                'is_penerima_kip' => true,
                'is_penerima_kks' => true,
                'is_layak_pip' => true,
                'pekerjaan_ayah' => 'Buruh Harian Lepas',
                'pekerjaan_ibu' => 'Petani',
                'penghasilan_ayah' => 900000,
                'penghasilan_ibu' => 400000,
                'jml_saudara_kandung' => 5,
                'jarak_rumah_km' => 15.2,
                'status_keaktifan' => 'Aktif',
                'rerata_nilai' => 69.8,
                'mapel_kkm' => 4,
                'alfa' => 14,
                'izin' => 5,
                'sakit' => 3,
                'target' => 'Tinggi',
            ],
            [
                'kelas_id' => $kelasXAk->id,
                'nisn' => '0073350811',
                'nipd' => '23241003',
                'nama_lengkap' => 'Cristin Ayu Lita Br Manullang',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Bungo',
                'tanggal_lahir' => '2007-08-14',
                'alamat' => 'Kec. Rimbo Tengah',
                'alat_transportasi' => 'Sepeda Motor',
                'is_penerima_kps' => false,
                'is_penerima_kip' => false,
                'is_penerima_kks' => false,
                'is_layak_pip' => false,
                'pekerjaan_ayah' => 'PNS / Guru',
                'pekerjaan_ibu' => 'Pedagang',
                'penghasilan_ayah' => 4500000,
                'penghasilan_ibu' => 2000000,
                'jml_saudara_kandung' => 1,
                'jarak_rumah_km' => 4.0,
                'status_keaktifan' => 'Aktif',
                'rerata_nilai' => 85.4,
                'mapel_kkm' => 0,
                'alfa' => 0,
                'izin' => 1,
                'sakit' => 2,
                'target' => 'Rendah',
            ],
            [
                'kelas_id' => $kelasXAk->id,
                'nisn' => '0071407417',
                'nipd' => '23241004',
                'nama_lengkap' => 'Devi Sri Rahayu',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Pelepat',
                'tanggal_lahir' => '2007-02-18',
                'alamat' => 'Dusun Rantau Keloyang',
                'alat_transportasi' => 'Jalan Kaki / Numpang',
                'is_penerima_kps' => true,
                'is_penerima_kip' => true,
                'is_penerima_kks' => false,
                'is_layak_pip' => true,
                'pekerjaan_ayah' => 'Tidak Bekerja / Sakit',
                'pekerjaan_ibu' => 'Buruh Cuci',
                'penghasilan_ayah' => 0,
                'penghasilan_ibu' => 650000,
                'jml_saudara_kandung' => 4,
                'jarak_rumah_km' => 18.0,
                'status_keaktifan' => 'Aktif',
                'rerata_nilai' => 71.0,
                'mapel_kkm' => 3,
                'alfa' => 16,
                'izin' => 6,
                'sakit' => 4,
                'target' => 'Tinggi',
            ],
            [
                'kelas_id' => $kelasBdg->id,
                'nisn' => '0076823524',
                'nipd' => '23241005',
                'nama_lengkap' => 'Dewi Anita',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Muara Bungo',
                'tanggal_lahir' => '2007-09-09',
                'alamat' => 'Pasar Bawah Bungo',
                'alat_transportasi' => 'Sepeda Motor',
                'is_penerima_kps' => false,
                'is_penerima_kip' => false,
                'is_penerima_kks' => false,
                'is_layak_pip' => false,
                'pekerjaan_ayah' => 'Karyawan Swasta',
                'pekerjaan_ibu' => 'IRT',
                'penghasilan_ayah' => 3200000,
                'penghasilan_ibu' => 0,
                'jml_saudara_kandung' => 2,
                'jarak_rumah_km' => 2.8,
                'status_keaktifan' => 'Aktif',
                'rerata_nilai' => 81.2,
                'mapel_kkm' => 0,
                'alfa' => 2,
                'izin' => 2,
                'sakit' => 1,
                'target' => 'Rendah',
            ],
            [
                'kelas_id' => $kelasDkv->id,
                'nisn' => '0085284268',
                'nipd' => '23241006',
                'nama_lengkap' => 'Echa Nabila',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Tanah Sepenggal',
                'tanggal_lahir' => '2008-01-22',
                'alamat' => 'Dusun Lubuk Landai',
                'alat_transportasi' => 'Angkot',
                'is_penerima_kps' => true,
                'is_penerima_kip' => true,
                'is_penerima_kks' => false,
                'is_layak_pip' => true,
                'pekerjaan_ayah' => 'Petani Karet',
                'pekerjaan_ibu' => 'Petani',
                'penghasilan_ayah' => 1100000,
                'penghasilan_ibu' => 300000,
                'jml_saudara_kandung' => 3,
                'jarak_rumah_km' => 21.5,
                'status_keaktifan' => 'Aktif',
                'rerata_nilai' => 70.5,
                'mapel_kkm' => 3,
                'alfa' => 12,
                'izin' => 4,
                'sakit' => 2,
                'target' => 'Tinggi',
            ],
        ];

        // 2. Tambahkan variasi sampel populasi hingga 50 siswa untuk pelatihan data mining KDD
        $names = [
            'Budi Santoso', 'Cahyo Wicaksono', 'Dimas Pratama', 'Endang Sri', 'Fajar Ramadhan',
            'Gilang Saputra', 'Hendra Wijaya', 'Indra Gunawan', 'Joko Susilo', 'Kurniawan Dwi',
            'Lestari Putri', 'Mega Utami', 'Nugroho Adi', 'Oki Setiawan', 'Panji Gumilang',
            'Qori Fadilah', 'Rian Hidayat', 'Siti Rahmawati', 'Taufik Ismail', 'Umar Bakri',
            'Vina Panduwinata', 'Wahyu Hidayat', 'Yanto Basna', 'Zainal Abidin', 'Agus Salim',
            'Bayu Skak', 'Cindy Permata', 'Doni Tata', 'Erwin Prasetya', 'Fitri Handayani',
            'Gita Gutawa', 'Hadi Pranoto', 'Irfan Hakim', 'Jessica Mila', 'Kevin Sanjaya',
            'Luna Maya', 'Marcell Siahaan', 'Nabila Syakieb', 'Onky Alexander', 'Prilly Latuconsina',
            'Raffi Ahmad', 'Syahrini', 'Tiara Andini', 'Vicky Prasetyo', 'Wulan Guritno'
        ];

        foreach ($specificSiswa as $item) {
            $this->createSiswaRecord($item);
        }

        foreach ($names as $idx => $name) {
            $isHighRisk = ($idx % 3 === 0); // 33% rasio risiko tinggi
            $targetKelas = $kelasList[$idx % $kelasList->count()];

            $item = [
                'kelas_id' => $targetKelas->id,
                'nisn' => sprintf('008%07d', $idx + 10),
                'nipd' => sprintf('2324%04d', $idx + 10),
                'nama_lengkap' => $name,
                'jenis_kelamin' => ($idx % 2 === 0) ? 'L' : 'P',
                'tempat_lahir' => 'Bungo',
                'tanggal_lahir' => '2007-06-15',
                'alamat' => 'Kec. Bathin III, Kab. Bungo',
                'alat_transportasi' => $isHighRisk ? 'Jalan Kaki' : 'Sepeda Motor',
                'is_penerima_kps' => $isHighRisk,
                'is_penerima_kip' => $isHighRisk,
                'is_penerima_kks' => $isHighRisk,
                'is_layak_pip' => $isHighRisk,
                'pekerjaan_ayah' => $isHighRisk ? 'Buruh Lepas' : 'Wiraswasta / Pegawai',
                'pekerjaan_ibu' => 'Ibu Rumah Tangga',
                'penghasilan_ayah' => $isHighRisk ? rand(600000, 1200000) : rand(2500000, 6000000),
                'penghasilan_ibu' => $isHighRisk ? 0 : rand(500000, 2000000),
                'jml_saudara_kandung' => $isHighRisk ? rand(3, 6) : rand(1, 3),
                'jarak_rumah_km' => $isHighRisk ? rand(11, 25) : rand(1, 7),
                'status_keaktifan' => 'Aktif',
                'rerata_nilai' => $isHighRisk ? rand(66, 73) : rand(78, 92),
                'mapel_kkm' => $isHighRisk ? rand(2, 5) : 0,
                'alfa' => $isHighRisk ? rand(9, 20) : rand(0, 3),
                'izin' => rand(1, 4),
                'sakit' => rand(1, 3),
                'target' => $isHighRisk ? 'Tinggi' : 'Rendah',
            ];

            $this->createSiswaRecord($item);
        }
    }

    private function createSiswaRecord(array $d): void
    {
        $siswa = Siswa::updateOrCreate(
            ['nisn' => $d['nisn']],
            [
                'kelas_id' => $d['kelas_id'],
                'nipd' => $d['nipd'],
                'nama_lengkap' => $d['nama_lengkap'],
                'jenis_kelamin' => $d['jenis_kelamin'],
                'tempat_lahir' => $d['tempat_lahir'],
                'tanggal_lahir' => $d['tanggal_lahir'],
                'alamat' => $d['alamat'],
                'alat_transportasi' => $d['alat_transportasi'],
                'is_penerima_kps' => $d['is_penerima_kps'],
                'is_penerima_kip' => $d['is_penerima_kip'],
                'is_penerima_kks' => $d['is_penerima_kks'],
                'is_layak_pip' => $d['is_layak_pip'],
                'pekerjaan_ayah' => $d['pekerjaan_ayah'],
                'pekerjaan_ibu' => $d['pekerjaan_ibu'],
                'penghasilan_ayah' => $d['penghasilan_ayah'],
                'penghasilan_ibu' => $d['penghasilan_ibu'],
                'jml_saudara_kandung' => $d['jml_saudara_kandung'],
                'jarak_rumah_km' => $d['jarak_rumah_km'],
                'status_keaktifan' => $d['status_keaktifan'],
            ]
        );

        // Tambah Rapor Semester
        RaporSemester::updateOrCreate(
            [
                'siswa_id' => $siswa->id,
                'semester' => 'Ganjil',
                'tahun_ajaran' => '2023/2024'
            ],
            [
                'rerata_nilai' => $d['rerata_nilai'],
                'jml_mapel_dibawah_kkm' => $d['mapel_kkm'],
                'total_sakit' => $d['sakit'],
                'total_izin' => $d['izin'],
                'total_alfa' => $d['alfa'],
            ]
        );

        // Tambah Fitur Dataset KDD
        $kodeEkonomi = ($d['is_layak_pip'] || $d['is_penerima_kip']) ? 1 : (($d['penghasilan_ayah'] < 3000000) ? 2 : 3);
        FiturDataset::updateOrCreate(
            [
                'siswa_id' => $siswa->id,
                'periode' => '2023/2024-Ganjil',
            ],
            [
                'rerata_nilai' => $d['rerata_nilai'],
                'mapel_dibawah_kkm' => $d['mapel_kkm'],
                'jml_alfa' => $d['alfa'],
                'jml_izin_sakit' => $d['izin'] + $d['sakit'],
                'kode_ekonomi' => $kodeEkonomi,
                'jarak_km' => $d['jarak_rumah_km'],
                'jml_saudara' => $d['jml_saudara_kandung'],
                'label_aktual' => $d['target'],
            ]
        );
    }
}
