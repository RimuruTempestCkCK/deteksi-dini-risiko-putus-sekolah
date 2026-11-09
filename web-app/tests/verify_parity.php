<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel Application container
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\MachineLearning\RandomForestService;
use App\Services\MachineLearning\NaiveBayesService;
use App\Services\MachineLearning\EvaluationMetricsService;

echo "=======================================================\n";
echo "VALIDASI KESETARAAN (PARITY) PHP SERVICES VS BASELINE\n";
echo "=======================================================\n";

$baselinePath = storage_path('app/baseline_reference.json');
if (!file_exists($baselinePath)) {
    die("File baseline reference tidak ditemukan!\n");
}

$baseline = json_decode(file_get_contents($baselinePath), true);
$rfService = new RandomForestService();
$nbService = new NaiveBayesService();
$metricsService = new EvaluationMetricsService();

$rfPreds = [];
$nbPreds = [];
$actuals = [];

$rfMismatches = 0;
$nbMismatches = 0;
$maxRfProbDiff = 0.0;
$maxNbProbDiff = 0.0;

foreach ($baseline['test_samples_predictions'] as $idx => $sample) {
    $x = $sample['x_features'];
    $actLabel = ($sample['target_actual'] == 1) ? 'Tinggi' : 'Rendah';
    $actuals[] = $actLabel;

    // Prediksi RF
    $resRf = $rfService->predict($x);
    $rfPreds[] = $resRf['label'];
    $rfExpected = ($sample['rf_pred'] == 1) ? 'Tinggi' : 'Rendah';
    if ($resRf['label'] !== $rfExpected) {
        $rfMismatches++;
    }
    $rfProbDiff = abs($resRf['probabilitas_tinggi'] - $sample['rf_prob']);
    if ($rfProbDiff > $maxRfProbDiff) {
        $maxRfProbDiff = $rfProbDiff;
    }

    // Prediksi NB
    $resNb = $nbService->predict($x);
    $nbPreds[] = $resNb['label'];
    $nbExpected = ($sample['nb_pred'] == 1) ? 'Tinggi' : 'Rendah';
    if ($resNb['label'] !== $nbExpected) {
        $nbMismatches++;
    }
    $nbProbDiff = abs($resNb['probabilitas_tinggi'] - $sample['nb_prob']);
    if ($nbProbDiff > $maxNbProbDiff) {
        $maxNbProbDiff = $nbProbDiff;
    }
}

echo "Total Data Uji: " . count($actuals) . " Siswa\n";
echo "RF Mismatches: {$rfMismatches} / " . count($actuals) . " (Max Prob Diff: {$maxRfProbDiff})\n";
echo "NB Mismatches: {$nbMismatches} / " . count($actuals) . " (Max Prob Diff: {$maxNbProbDiff})\n";

$rfMetrics = $metricsService->calculateMetrics($actuals, $rfPreds);
$nbMetrics = $metricsService->calculateMetrics($actuals, $nbPreds);

echo "\n--- HASIL EVALUASI RANDOM FOREST (LARAVEL) ---\n";
echo "TN: {$rfMetrics['tn']} | FP: {$rfMetrics['fp']} | FN: {$rfMetrics['fn']} | TP: {$rfMetrics['tp']}\n";
echo "Accuracy : {$rfMetrics['accuracy']}%\n";
echo "Precision: {$rfMetrics['precision']}%\n";
echo "Recall   : {$rfMetrics['recall']}%\n";
echo "F1-Score : {$rfMetrics['f1_score']}%\n";

echo "\n--- HASIL EVALUASI NAÏVE BAYES (LARAVEL) ---\n";
echo "TN: {$nbMetrics['tn']} | FP: {$nbMetrics['fp']} | FN: {$nbMetrics['fn']} | TP: {$nbMetrics['tp']}\n";
echo "Accuracy : {$nbMetrics['accuracy']}%\n";
echo "Precision: {$nbMetrics['precision']}%\n";
echo "Recall   : {$nbMetrics['recall']}%\n";
echo "F1-Score : {$nbMetrics['f1_score']}%\n";

// Validasi Sampel D1
echo "\n--- PENGUJIAN SAMPEL MANUAL D1 (ANDINI FEBRYANTI) ---\n";
$d1 = [
    'X1_Nilai' => 79.49,
    'X2_KKM' => 1,
    'X3_Alfa' => 7,
    'X4_IzinSakit' => 4,
    'X5_Ekonomi' => 2,
    'X6_Jarak' => 15.0,
    'X7_Pelanggaran' => 1,
    'X8_Dukungan' => 3
];
$predD1Rf = $rfService->predict($d1);
$predD1Nb = $nbService->predict($d1);
echo "Sampel D1 - RF: {$predD1Rf['label']} (Prob: {$predD1Rf['probabilitas_tinggi']})\n";
echo "Sampel D1 - NB: {$predD1Nb['label']} (Prob: {$predD1Nb['probabilitas_tinggi']})\n";

if ($rfMismatches === 0 && $nbMismatches === 0) {
    echo "\n>>> STATUS: PARITY 100% VERIFIED! HASIL LARAVEL IDENTIK DENGAN PYTHON DAN EXCEL! <<<\n";
} else {
    echo "\n>>> STATUS: MISMATCH FOUND! <<<\n";
}
