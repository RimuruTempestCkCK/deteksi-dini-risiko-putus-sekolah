<?php

namespace App\Services\MachineLearning;

class RandomForestService
{
    protected int $numTrees = 100;
    protected int $maxDepth = 5;
    protected array $featureCols = [
        'X1_Nilai', 'X2_KKM', 'X3_Alfa', 'X4_IzinSakit',
        'X5_Ekonomi', 'X6_Jarak', 'X7_Pelanggaran', 'X8_Dukungan'
    ];
    protected array $trees = [];
    protected array $featureImportances = [];

    public function __construct(int $numTrees = 100, int $maxDepth = 5)
    {
        $this->numTrees = $numTrees;
        $this->maxDepth = $maxDepth;
        $this->loadStateFromStorage();
    }

    public function loadState(array $state): void
    {
        if (isset($state['trees'])) {
            $this->trees = $state['trees'];
            $this->numTrees = count($this->trees);
        }
        if (isset($state['feature_importances'])) {
            $this->featureImportances = $state['feature_importances'];
        }
        if (isset($state['max_depth'])) {
            $this->maxDepth = $state['max_depth'];
        }
    }

    public function loadStateFromStorage(): bool
    {
        $path = storage_path('app/rf_model_state.json');
        if (file_exists($path)) {
            $content = file_get_contents($path);
            $state = json_decode($content, true);
            if ($state) {
                $this->loadState($state);
                return true;
            }
        }
        return false;
    }

    public function getFeatureImportances(): array
    {
        return $this->featureImportances;
    }

    public function getTrees(): array
    {
        return $this->trees;
    }

    /**
     * Prediksi sampel menggunakan Random Forest 100 Decision Trees
     * $sample: associative array (keys: X1_Nilai, X2_KKM, ...) atau sequential array of 8 values
     */
    public function predict(array $sample): array
    {
        if (empty($this->trees)) {
            $this->loadStateFromStorage();
        }

        if (empty($this->trees)) {
            return [
                'label' => 'Rendah',
                'probabilitas' => 0.0,
                'probabilitas_tinggi' => 0.0,
                'probabilitas_rendah' => 1.0,
                'votes_tinggi' => 0,
                'total_trees' => 0
            ];
        }

        // Format sample ke sequential array 8 fitur sesuai urutan featureCols
        $xVec = [];
        if (isset($sample['X1_Nilai'])) {
            foreach ($this->featureCols as $fc) {
                $xVec[] = (float) ($sample[$fc] ?? 0.0);
            }
        } else {
            // Asumsi indexed 0..7
            $xVec = array_map('floatval', array_values($sample));
        }

        $totalTrees = count($this->trees);
        $sumProb1 = 0.0;

        foreach ($this->trees as $tree) {
            $node = 0;
            while (isset($tree['children_left'][$node]) && $tree['children_left'][$node] != -1) {
                $feat = (int) $tree['feature'][$node];
                $th = (float) $tree['threshold'][$node];
                $val = (float) $xVec[$feat];

                // Memperhitungkan presisi komparasi float32 scikit-learn
                $valF32 = (float) sprintf('%.7g', $val);

                if ($valF32 <= $th) {
                    $node = (int) $tree['children_left'][$node];
                } else {
                    $node = (int) $tree['children_right'][$node];
                }
            }

            // Ambil probabilitas kelas 1 (Tinggi) dari daun
            $leafVal = $tree['value'][$node];
            $p1 = is_array($leafVal) ? (float) $leafVal[1] : 0.0;
            $sumProb1 += $p1;
        }

        $probTinggi = $sumProb1 / ($totalTrees > 0 ? $totalTrees : 1);
        $probRendah = 1.0 - $probTinggi;
        $label = ($probTinggi >= 0.5) ? 'Tinggi' : 'Rendah';

        return [
            'label' => $label,
            'probabilitas' => round($probTinggi, 4),
            'probabilitas_tinggi' => round($probTinggi, 6),
            'probabilitas_rendah' => round($probRendah, 6),
            'percent_tinggi' => round($probTinggi * 100, 2),
            'percent_rendah' => round($probRendah * 100, 2),
            'total_trees' => $totalTrees,
        ];
    }
}
