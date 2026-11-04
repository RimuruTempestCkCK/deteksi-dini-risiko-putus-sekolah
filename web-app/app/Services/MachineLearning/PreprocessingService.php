<?php

namespace App\Services\MachineLearning;

class PreprocessingService
{
    /**
     * Ekstraksi atribut siswa & rapor menjadi vektor fitur klasifikasi KDD
     * Fitur:
     * 0: rerata_nilai (float)
     * 1: mapel_dibawah_kkm (int)
     * 2: jml_alfa (int)
     * 3: jml_izin_sakit (int)
     * 4: kode_ekonomi (1: sangat rentan/KIP/PIP, 2: menengah, 3: mampu)
     * 5: jarak_km (float)
     * 6: jml_saudara (int)
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
        } elseif ($siswa->raporSemesters && $siswa->raporSemesters->count() > 0) {
            $latestRapor = $siswa->raporSemesters->sortByDesc('id')->first();
            $rerataNilai = (float) $latestRapor->rerata_nilai;
            $mapelBawahKkm = (int) $latestRapor->jml_mapel_dibawah_kkm;
            $jmlAlfa = (int) $latestRapor->total_alfa;
            $jmlIzinSakit = (int) ($latestRapor->total_sakit + $latestRapor->total_izin);
        }

        // Tentukan tingkat kerentanan ekonomi (1: Rentan, 2: Menengah, 3: Mampu)
        $penghasilanTotal = (float) $siswa->penghasilan_ayah + (float) $siswa->penghasilan_ibu;
        $isBansos = $siswa->is_penerima_kip || $siswa->is_penerima_kps || $siswa->is_layak_pip || $siswa->is_penerima_kks;

        if ($isBansos || $penghasilanTotal < 1500000) {
            $kodeEkonomi = 1;
        } elseif ($penghasilanTotal <= 3500000) {
            $kodeEkonomi = 2;
        } else {
            $kodeEkonomi = 3;
        }

        return [
            'rerata_nilai' => $rerataNilai,
            'mapel_dibawah_kkm' => $mapelBawahKkm,
            'jml_alfa' => $jmlAlfa,
            'jml_izin_sakit' => $jmlIzinSakit,
            'kode_ekonomi' => $kodeEkonomi,
            'jarak_km' => (float) $siswa->jarak_rumah_km,
            'jml_saudara' => (int) $siswa->jml_saudara_kandung,
        ];
    }

    /**
     * Identifikasi faktor-faktor kunci pemicu risiko
     */
    public function identifyRiskFactors(array $features): string
    {
        $factors = [];

        if ($features['jml_alfa'] >= 10) {
            $factors[] = "Ketidakhadiran tanpa keterangan (Alfa) sangat tinggi ({$features['jml_alfa']} hari)";
        } elseif ($features['jml_alfa'] >= 5) {
            $factors[] = "Tingkat ketidakhadiran Alfa perlu perhatian ({$features['jml_alfa']} hari)";
        }

        if ($features['mapel_dibawah_kkm'] >= 3) {
            $factors[] = "Terdapat {$features['mapel_dibawah_kkm']} mata pelajaran belum tuntas/di bawah KKM";
        }

        if ($features['rerata_nilai'] < 75.0) {
            $factors[] = "Rerata nilai rapor berada di bawah standar (" . number_format($features['rerata_nilai'], 1) . ")";
        }

        if ($features['kode_ekonomi'] == 1) {
            $factors[] = "Keluarga terindikasi rentan finansial / penerima bantuan sosial (PIP/KIP)";
        }

        if ($features['jarak_km'] >= 12.0) {
            $factors[] = "Akses jarak rumah ke sekolah sangat jauh (" . number_format($features['jarak_km'], 1) . " KM)";
        }

        if (empty($factors)) {
            return "Kondisi akademik dan presensi dalam kategori aman & kondusif.";
        }

        return implode(', ', $factors);
    }
}
