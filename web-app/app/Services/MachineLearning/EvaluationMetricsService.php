<?php

namespace App\Services\MachineLearning;

class EvaluationMetricsService
{
    /**
     * Hitung Confusion Matrix & Metrik Evaluasi:
     * Accuracy, Precision, Recall, F1-Score
     */
    public function calculateMetrics(array $actuals, array $predictions): array
    {
        $tp = 0; // Actual: Tinggi, Pred: Tinggi
        $fp = 0; // Actual: Rendah, Pred: Tinggi
        $tn = 0; // Actual: Rendah, Pred: Rendah
        $fn = 0; // Actual: Tinggi, Pred: Rendah

        $total = count($actuals);
        if ($total === 0) {
            return [
                'tp' => 0, 'fp' => 0, 'tn' => 0, 'fn' => 0,
                'accuracy' => 0, 'precision' => 0, 'recall' => 0, 'f1_score' => 0
            ];
        }

        for ($i = 0; $i < $total; $i++) {
            $act = $actuals[$i];
            $pred = $predictions[$i];

            if ($act === 'Tinggi' && $pred === 'Tinggi') {
                $tp++;
            } elseif ($act === 'Rendah' && $pred === 'Tinggi') {
                $fp++;
            } elseif ($act === 'Rendah' && $pred === 'Rendah') {
                $tn++;
            } elseif ($act === 'Tinggi' && $pred === 'Rendah') {
                $fn++;
            }
        }

        $accuracy = ($tp + $tn) / ($total > 0 ? $total : 1);
        $precision = ($tp + $fp) > 0 ? ($tp / ($tp + $fp)) : 0;
        $recall = ($tp + $fn) > 0 ? ($tp / ($tp + $fn)) : 0;
        $f1Score = ($precision + $recall) > 0 ? (2 * ($precision * $recall) / ($precision + $recall)) : 0;

        return [
            'tp' => $tp,
            'fp' => $fp,
            'tn' => $tn,
            'fn' => $fn,
            'accuracy' => round($accuracy * 100, 2),
            'precision' => round($precision * 100, 2),
            'recall' => round($recall * 100, 2),
            'f1_score' => round($f1Score * 100, 2),
        ];
    }
}
