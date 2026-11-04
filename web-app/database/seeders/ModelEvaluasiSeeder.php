<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FiturDataset;
use App\Models\ModelEvaluasi;
use App\Services\MachineLearning\RandomForestService;
use App\Services\MachineLearning\NaiveBayesService;
use App\Services\MachineLearning\EvaluationMetricsService;

class ModelEvaluasiSeeder extends Seeder
{
    public function run(): void
    {
        $datasets = FiturDataset::all();
        if ($datasets->isEmpty()) return;

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

        // Split 80% Latih : 20% Uji
        $total = count($samples);
        $trainCount = (int) round($total * 0.8);

        $trainSamples = array_slice($samples, 0, $trainCount);
        $trainLabels = array_slice($labels, 0, $trainCount);

        $testSamples = array_slice($samples, $trainCount);
        $testLabels = array_slice($labels, $trainCount);

        $metricsService = new EvaluationMetricsService();

        // 1. Train Random Forest (Metode Utama)
        $rfService = new RandomForestService(30, 6);
        $rfState = $rfService->train($trainSamples, $trainLabels);
        $rfPreds = [];
        foreach ($testSamples as $ts) {
            $pred = $rfService->predict($ts);
            $rfPreds[] = $pred['label'];
        }
        $rfMetrics = $metricsService->calculateMetrics($testLabels, $rfPreds);

        ModelEvaluasi::create([
            'algoritma' => 'Random_Forest',
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
                'split_ratio' => '80:20',
                'criterion' => 'gini_impurity'
            ],
            'model_state' => json_encode($rfState),
            'is_active' => true, // Menjadi model aktif default
        ]);

        // 2. Train Naive Bayes (Metode Komparasi)
        $nbService = new NaiveBayesService();
        $nbState = $nbService->train($trainSamples, $trainLabels);
        $nbPreds = [];
        foreach ($testSamples as $ts) {
            $pred = $nbService->predict($ts);
            $nbPreds[] = $pred['label'];
        }
        $nbMetrics = $metricsService->calculateMetrics($testLabels, $nbPreds);

        ModelEvaluasi::create([
            'algoritma' => 'Naive_Bayes',
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
                'split_ratio' => '80:20',
                'variance_smoothing' => 1e-4
            ],
            'model_state' => json_encode($nbState),
            'is_active' => false,
        ]);
    }
}
