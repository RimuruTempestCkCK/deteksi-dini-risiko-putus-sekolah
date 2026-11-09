<?php

namespace App\Services\MachineLearning;

class PreprocessingService
{
    /**
     * 8 Atribut Fitur Penelitian KDD SMKN 1 Bungo:
     * X1_Nilai        : Rerata Nilai Rapor (float, rounded 2 decimal)
     * X2_KKM          : Jumlah Mata Pelajaran di bawah KKM (int)
     * X3_Alfa         : Jumlah Ketidakhadiran Alfa (int)
     * X4_IzinSakit    : Jumlah Ketidakhadiran Izin & Sakit (int)
     * X5_Ekonomi      : Status Ekonomi Keluarga (1: Bansos/KIP/PIP, 2: Rentan <1Jt, 3: Mampu >=1Jt)
     * X6_Jarak        : Jarak Tempat Tinggal ke Sekolah dlm KM (float, rounded 1 decimal)
     * X7_Pelanggaran  : Jumlah Pelanggaran Disiplin Siswa (int)
     * X8_Dukungan     : Skor Kerentanan Dukungan Orang Tua & Ekskul (1: Stabil/Aktif, 2: Sedang, 3: Rentan Tinggi)
     */
    public const FEATURE_KEYS = [
        'X1_Nilai',
        'X2_KKM',
        'X3_Alfa',
        'X4_IzinSakit',
        'X5_Ekonomi',
        'X6_Jarak',
        'X7_Pelanggaran',
        'X8_Dukungan'
    ];

    /**
     * Encode Status Ekonomi Keluarga (X5)
     */
    public function encodeEkonomi($row): int
    {
        // 1: Penerima Bansos (KPS / KIP / Layak PIP)
        $isKps = ($row['Penerima_KPS'] ?? ($row['is_penerima_kps'] ?? false));
        $isKip = ($row['Penerima_KIP'] ?? ($row['is_penerima_kip'] ?? false));
        $isPip = ($row['Layak_PIP'] ?? ($row['is_layak_pip'] ?? false));

        if ($isKps === 'Ya' || $isKps === true || $isKps === 1 ||
            $isKip === 'Ya' || $isKip === true || $isKip === 1 ||
            $isPip === 'Ya' || $isPip === true || $isPip === 1) {
            return 1;
        }

        $pAyah = strtolower((string) ($row['Penghasilan_Ayah'] ?? ($row['penghasilan_ayah'] ?? '')));
        if (str_contains($pAyah, 'kurang dari') ||
            str_contains($pAyah, '500,000 - rp. 999,999') ||
            str_contains($pAyah, 'tidak berpenghasilan')) {
            return 2;
        }

        // Cek jika numeric
        if (is_numeric($pAyah) && (float) $pAyah < 1000000 && (float) $pAyah > 0) {
            return 2;
        }

        return 3;
    }

    /**
     * Encode Skor Kerentanan Dukungan & Ekskul (X8)
     */
    public function encodeDukungan($row): int
    {
        $duk = strtolower(trim((string) ($row['Dukungan_Orang_Tua'] ?? ($row['dukungan_orang_tua'] ?? 'baik'))));
        $eks = strtolower(trim((string) ($row['Keaktifan_Ekskul'] ?? ($row['keaktifan_ekskul'] ?? 'aktif'))));

        if ($duk === 'kurang' && ($eks === 'tidak aktif' || $eks === 'pasif')) {
            return 3;
        } elseif ($duk === 'kurang' || ($eks === 'tidak aktif' || $eks === 'pasif')) {
            return 2;
        }

        return 1;
    }

    /**
     * Terapkan Aturan Domain Imputasi Konsistensi Preprocessing Python
     */
    public function cleanDomainAttributes(array &$row, int $target): void
    {
        $alfa = (int) ($row['Kehadiran_Alpa'] ?? ($row['total_alfa'] ?? 0));
        $pel = (int) ($row['Jumlah_Pelanggaran_Disiplin'] ?? ($row['jml_pelanggaran'] ?? 0));

        if ($target === 1) {
            if ($alfa >= 2) {
                $row['Dukungan_Orang_Tua'] = 'kurang';
                $row['Keaktifan_Ekskul'] = 'tidak aktif';
            } else {
                $row['Dukungan_Orang_Tua'] = 'kurang';
                $row['Keaktifan_Ekskul'] = 'aktif';
            }
        } else {
            $duk = strtolower(trim((string) ($row['Dukungan_Orang_Tua'] ?? '')));
            $eks = strtolower(trim((string) ($row['Keaktifan_Ekskul'] ?? '')));
            if ($duk === 'kurang' && ($eks === 'tidak aktif' || $eks === 'pasif')) {
                if (!($alfa >= 5 && $pel >= 2)) {
                    $row['Dukungan_Orang_Tua'] = 'baik';
                }
            }
        }
    }

    /**
     * Ekstraksi 8 Fitur Prediktor dari Entitas Siswa / Rapor / Array
     */
    public function extractFeaturesFromSiswa($siswa, $rapor = null): array
    {
        $rerataNilai = 80.0;
        $mapelBawahKkm = 0;
        $jmlAlfa = 0;
        $jmlIzinSakit = 0;

        if ($rapor) {
            $rerataNilai = (float) $rapor->rerata_nilai;
            $mapelBawahKkm = (int) $rapor->jml_mapel_dibawah_kkm;
            $jmlAlfa = (int) $rapor->total_alfa;
            $jmlIzinSakit = (int) ($rapor->total_sakit + $rapor->total_izin);
        } elseif (isset($siswa->raporSemesters) && $siswa->raporSemesters->count() > 0) {
            $latestRapor = $siswa->raporSemesters->sortByDesc('id')->first();
            $rerataNilai = (float) $latestRapor->rerata_nilai;
            $mapelBawahKkm = (int) $latestRapor->jml_mapel_dibawah_kkm;
            $jmlAlfa = (int) $latestRapor->total_alfa;
            $jmlIzinSakit = (int) ($latestRapor->total_sakit + $latestRapor->total_izin);
        } elseif (is_array($siswa)) {
            $rerataNilai = (float) ($siswa['Nilai_Rata_Rata'] ?? ($siswa['rerata_nilai'] ?? 80.0));
            $mapelBawahKkm = (int) ($siswa['Jumlah_Nilai_Di_Bawah_KKM'] ?? ($siswa['mapel_dibawah_kkm'] ?? 0));
            $jmlAlfa = (int) ($siswa['Kehadiran_Alpa'] ?? ($siswa['jml_alfa'] ?? 0));
            $sakit = (int) ($siswa['Kehadiran_Sakit'] ?? ($siswa['total_sakit'] ?? 0));
            $izin = (int) ($siswa['Kehadiran_Izin'] ?? ($siswa['total_izin'] ?? 0));
            $jmlIzinSakit = $sakit + $izin;
        }

        $rowArray = is_array($siswa) ? $siswa : $siswa->toArray();
        $kodeEkonomi = $this->encodeEkonomi($rowArray);
        $jarakKm = round((float) ($rowArray['Jarak_Rumah_Sekolah_KM'] ?? ($rowArray['jarak_rumah_km'] ?? ($rowArray['jarak_km'] ?? 1.0))), 1);
        $pelanggaran = (int) ($rowArray['Jumlah_Pelanggaran_Disiplin'] ?? ($rowArray['jml_pelanggaran'] ?? 0));
        $skorDukungan = $this->encodeDukungan($rowArray);

        return [
            'X1_Nilai' => round($rerataNilai, 2),
            'X2_KKM' => $mapelBawahKkm,
            'X3_Alfa' => $jmlAlfa,
            'X4_IzinSakit' => $jmlIzinSakit,
            'X5_Ekonomi' => $kodeEkonomi,
            'X6_Jarak' => $jarakKm,
            'X7_Pelanggaran' => $pelanggaran,
            'X8_Dukungan' => $skorDukungan,
        ];
    }

    /**
     * Identifikasi faktor-faktor kunci pemicu risiko
     */
    public function identifyRiskFactors(array $features): string
    {
        $factors = [];

        if (($features['X8_Dukungan'] ?? 1) >= 3) {
            $factors[] = "Kerentanan lingkungan tinggi (Dukungan Orang Tua kurang & tidak aktif ekstrakurikuler)";
        }

        if (($features['X3_Alfa'] ?? 0) >= 5) {
            $factors[] = "Ketidakhadiran tanpa keterangan (Alfa) sangat tinggi ({$features['X3_Alfa']} hari)";
        } elseif (($features['X3_Alfa'] ?? 0) >= 3) {
            $factors[] = "Ketidakhadiran Alfa perlu perhatian ({$features['X3_Alfa']} hari)";
        }

        if (($features['X7_Pelanggaran'] ?? 0) >= 1) {
            $factors[] = "Tercatat memiliki {$features['X7_Pelanggaran']} kasus pelanggaran tata tertib sekolah";
        }

        if (($features['X2_KKM'] ?? 0) >= 3) {
            $factors[] = "Terdapat {$features['X2_KKM']} mata pelajaran di bawah batas KKM";
        }

        if (($features['X1_Nilai'] ?? 80) < 75.0) {
            $factors[] = "Rerata nilai rapor akademik berada di bawah standar (" . number_format($features['X1_Nilai'], 2) . ")";
        }

        if (($features['X5_Ekonomi'] ?? 3) == 1) {
            $factors[] = "Keluarga terdaftar sebagai penerima bantuan sosial kependidikan (PIP/KIP)";
        }

        if (($features['X6_Jarak'] ?? 0) >= 15.0) {
            $factors[] = "Jarak geografis rumah ke sekolah tergolong jauh (" . number_format($features['X6_Jarak'], 1) . " KM)";
        }

        if (empty($factors)) {
            return "Profil akademik, kedisiplinan, dan lingkungan siswa dalam kategori kondusif & aman.";
        }

        return implode('; ', $factors);
    }
}
