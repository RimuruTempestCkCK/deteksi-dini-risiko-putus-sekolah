<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\ModelEvaluasi;
use App\Models\PrediksiLog;
use App\Models\TindakanIntervensi;
use App\Models\User;
use App\Services\MachineLearning\RandomForestService;
use App\Services\MachineLearning\PreprocessingService;

class PrediksiSeeder extends Seeder
{
    public function run(): void
    {
        $rfModel = ModelEvaluasi::where('algoritma', 'Random_Forest')->first();
        if (!$rfModel) return;

        $admin = User::first();
        $rfService = new RandomForestService();
        $prepService = new PreprocessingService();

        $state = json_decode($rfModel->model_state, true);
        if ($state) {
            $rfService->loadState($state);
        }

        $siswas = Siswa::with('raporSemesters')->get();

        foreach ($siswas as $siswa) {
            $features = $prepService->extractFeaturesFromSiswa($siswa);
            $pred = $rfService->predict($features);
            $factors = $prepService->identifyRiskFactors($features);

            PrediksiLog::create([
                'siswa_id' => $siswa->id,
                'model_evaluasi_id' => $rfModel->id,
                'hasil_prediksi' => $pred['label'],
                'probabilitas_risiko' => $pred['probabilitas'],
                'faktor_kunci' => $factors,
                'tanggal_prediksi' => now()->toDateString(),
            ]);

            // Jika berisiko tinggi, buatkan 1 catatan intervensi pemantauan awal
            if ($pred['label'] === 'Tinggi') {
                TindakanIntervensi::create([
                    'siswa_id' => $siswa->id,
                    'user_id' => $admin ? $admin->id : null,
                    'tgl_tindakan' => now()->toDateString(),
                    'jenis_tindakan' => 'Konseling Individual & Konfirmasi Wali Siswa',
                    'catatan_penanganan' => "Terdeteksi berisiko putus sekolah (probabilitas: " . round($pred['probabilitas'] * 100, 1) . "%). Diberikan pendampingan motivasi belajar serta verifikasi berkas usulan PIP.",
                    'status_intervensi' => 'Dalam Pemantauan',
                ]);
            }
        }
    }
}
