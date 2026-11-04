<?php

namespace App\Http\Controllers;

use App\Models\FiturDataset;
use App\Models\ModelEvaluasi;
use App\Services\MachineLearning\RandomForestService;
use App\Services\MachineLearning\NaiveBayesService;
use App\Services\MachineLearning\EvaluationMetricsService;
use Illuminate\Http\Request;

class MiningController extends Controller
{
    public function index()
    {
        $totalFitur = FiturDataset::count();
        $tinggiCount = FiturDataset::where('label_aktual', 'Tinggi')->count();
        $rendahCount = FiturDataset::where('label_aktual', 'Rendah')->count();
        $datasets = FiturDataset::with('siswa.kelas')->paginate(15);
        $models = ModelEvaluasi::all();

        return view('mining.index', compact('totalFitur', 'tinggiCount', 'rendahCount', 'datasets', 'models'));
    }

    /**
     * Jalankan Training Pipeline KDD untuk Random Forest dan Naive Bayes
     */
    public function train(Request $request)
    {
        $splitRatio = (float) $request->input('split_ratio', 0.8); // 80% train
        $datasets = FiturDataset::all();

        if ($datasets->count() < 10) {
            return back()->with('error', 'Jumlah sampel dataset belum mencukupi (minimal 10 data) untuk menjalankan pipeline data mining.');
        }

        $samples = [];
        $labels = [];

        foreach ($datasets as $d) {
            $samples[] = [
                'rerata_nilai' => (float) $d->rerata_nilai,
                'mapel_dibawah_kkm' => (int) $d->mapel_dibawah_kkm,
                'jml_alfa' => (int) $d->jml_alfa,
                'jml_izin_sakit' => (int) $d->jml_izin_sakit,
                'kode_ekonomi' => (int) $d->kode_ekonomi,
                'jarak_km' => (float) $d->jarak_km,
                'jml_saudara' => (int) $d->jml_saudara,
            ];
            $labels[] = $d->label_aktual ?? 'Rendah';
        }

        // Stratified Split (80% Latih : 20% Uji) agar proporsi kelas Tinggi & Rendah terdistribusi seimbang
        $tinggiData = [];
        $rendahData = [];

        foreach ($datasets as $d) {
            $item = [
                'sample' => [
                    'rerata_nilai' => (float) $d->rerata_nilai,
                    'mapel_dibawah_kkm' => (int) $d->mapel_dibawah_kkm,
                    'jml_alfa' => (int) $d->jml_alfa,
                    'jml_izin_sakit' => (int) $d->jml_izin_sakit,
                    'kode_ekonomi' => (int) $d->kode_ekonomi,
                    'jarak_km' => (float) $d->jarak_km,
                    'jml_saudara' => (int) $d->jml_saudara,
                ],
                'label' => $d->label_aktual ?? 'Rendah',
            ];

            if ($item['label'] === 'Tinggi') {
                $tinggiData[] = $item;
            } else {
                $rendahData[] = $item;
            }
        }

        // Hitung split per strata
        $trainTinggiCount = max(1, (int) round(count($tinggiData) * $splitRatio));
        $trainRendahCount = max(1, (int) round(count($rendahData) * $splitRatio));

        $trainSet = array_merge(
            array_slice($tinggiData, 0, $trainTinggiCount),
            array_slice($rendahData, 0, $trainRendahCount)
        );

        $testSet = array_merge(
            array_slice($tinggiData, $trainTinggiCount),
            array_slice($rendahData, $trainRendahCount)
        );

        // Jika testSet tidak memiliki sampel Tinggi, pinjam 1 sampel untuk verifikasi data uji
        $hasTinggiInTest = false;
        foreach ($testSet as $ts) {
            if ($ts['label'] === 'Tinggi') {
                $hasTinggiInTest = true;
                break;
            }
        }
        if (!$hasTinggiInTest && count($tinggiData) > 1) {
            $testSet[] = array_pop($trainSet);
        }

        $trainSamples = array_column($trainSet, 'sample');
        $trainLabels = array_column($trainSet, 'label');

        $testSamples = array_column($testSet, 'sample');
        $testLabels = array_column($testSet, 'label');

        $metricsService = new EvaluationMetricsService();

        // 1. Training Random Forest
        $rfService = new RandomForestService(30, 6);
        $rfState = $rfService->train($trainSamples, $trainLabels);
        $rfPreds = [];
        foreach ($testSamples as $ts) {
            $pred = $rfService->predict($ts);
            $rfPreds[] = $pred['label'];
        }
        $rfMetrics = $metricsService->calculateMetrics($testLabels, $rfPreds);

        ModelEvaluasi::updateOrCreate(
            ['algoritma' => 'Random_Forest'],
            [
                'akurasi' => $rfMetrics['accuracy'],
                'presisi' => $rfMetrics['precision'],
                'recall' => $rfMetrics['recall'],
                'f1_score' => $rfMetrics['f1_score'],
                'confusion_matrix' => [
                    'tp' => $rfMetrics['tp'],
                    'fp' => $rfMetrics['fp'],
                    'tn' => $rfMetrics['tn'],
                    'fn' => $rfMetrics['fn'],
                ],
                'hyperparameter_settings' => [
                    'n_estimators' => 30,
                    'max_depth' => 6,
                    'split_ratio' => ($splitRatio * 100) . ':' . ((1 - $splitRatio) * 100),
                ],
                'model_state' => json_encode($rfState),
            ]
        );

        // 2. Training Naive Bayes
        $nbService = new NaiveBayesService();
        $nbState = $nbService->train($trainSamples, $trainLabels);
        $nbPreds = [];
        foreach ($testSamples as $ts) {
            $pred = $nbService->predict($ts);
            $nbPreds[] = $pred['label'];
        }
        $nbMetrics = $metricsService->calculateMetrics($testLabels, $nbPreds);

        ModelEvaluasi::updateOrCreate(
            ['algoritma' => 'Naive_Bayes'],
            [
                'akurasi' => $nbMetrics['accuracy'],
                'presisi' => $nbMetrics['precision'],
                'recall' => $nbMetrics['recall'],
                'f1_score' => $nbMetrics['f1_score'],
                'confusion_matrix' => [
                    'tp' => $nbMetrics['tp'],
                    'fp' => $nbMetrics['fp'],
                    'tn' => $nbMetrics['tn'],
                    'fn' => $nbMetrics['fn'],
                ],
                'hyperparameter_settings' => [
                    'type' => 'GaussianNB',
                    'split_ratio' => ($splitRatio * 100) . ':' . ((1 - $splitRatio) * 100),
                ],
                'model_state' => json_encode($nbState),
            ]
        );

        return redirect()->route('komparasi.index')->with('success', 'Pipeline Data Mining berhasil dieksekusi! Model Random Forest dan Naïve Bayes telah diperbarui.');
    }
}
