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

        $total = count($samples);
        $trainCount = (int) round($total * $splitRatio);

        $trainSamples = array_slice($samples, 0, $trainCount);
        $trainLabels = array_slice($labels, 0, $trainCount);

        $testSamples = array_slice($samples, $trainCount);
        $testLabels = array_slice($labels, $trainCount);

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
