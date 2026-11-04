<?php

namespace App\Services\MachineLearning;

class RandomForestService
{
    protected int $numTrees = 25;
    protected int $maxDepth = 6;
    protected int $minSamplesSplit = 2;
    protected array $trees = [];

    public function __construct(int $numTrees = 25, int $maxDepth = 6)
    {
        $this->numTrees = $numTrees;
        $this->maxDepth = $maxDepth;
    }

    /**
     * Latih ensemble Random Forest
     */
    public function train(array $samples, array $labels): array
    {
        $this->trees = [];
        $total = count($samples);
        if ($total === 0) return [];

        $featureKeys = array_keys($samples[0]);
        $mFeatures = max(2, (int) round(sqrt(count($featureKeys))));

        for ($t = 0; $t < $this->numTrees; $t++) {
            // 1. Bootstrap Sample (Sampling with replacement)
            $bootSamples = [];
            $bootLabels = [];
            for ($i = 0; $i < $total; $i++) {
                $randIdx = rand(0, $total - 1);
                $bootSamples[] = $samples[$randIdx];
                $bootLabels[] = $labels[$randIdx];
            }

            // 2. Build Decision Tree
            $tree = $this->buildTree($bootSamples, $bootLabels, 0, $featureKeys, $mFeatures);
            $this->trees[] = $tree;
        }

        return [
            'num_trees' => $this->numTrees,
            'max_depth' => $this->maxDepth,
            'trees' => $this->trees,
        ];
    }

    /**
     * Prediksi melalui Majority Voting seluruh Decision Tree
     */
    public function predict(array $sample): array
    {
        if (empty($this->trees)) {
            return ['label' => 'Rendah', 'probabilitas' => 0.0];
        }

        $votesTinggi = 0;
        $totalTrees = count($this->trees);

        foreach ($this->trees as $tree) {
            $pred = $this->predictTree($tree, $sample);
            if ($pred === 'Tinggi') {
                $votesTinggi++;
            }
        }

        $probTinggi = $votesTinggi / ($totalTrees ?: 1);
        $label = ($probTinggi >= 0.5) ? 'Tinggi' : 'Rendah';

        return [
            'label' => $label,
            'probabilitas' => round($probTinggi, 4),
            'votes_tinggi' => $votesTinggi,
            'total_trees' => $totalTrees,
        ];
    }

    protected function buildTree(array $samples, array $labels, int $depth, array $featureKeys, int $mFeatures): array
    {
        $numSamples = count($samples);
        $tinggiCount = 0;
        foreach ($labels as $l) {
            if ($l === 'Tinggi') $tinggiCount++;
        }
        $rendahCount = $numSamples - $tinggiCount;

        // Leaf Node Condition
        if ($depth >= $this->maxDepth || $numSamples < $this->minSamplesSplit || $tinggiCount === 0 || $rendahCount === 0) {
            return [
                'type' => 'leaf',
                'prediction' => ($tinggiCount >= $rendahCount) ? 'Tinggi' : 'Rendah',
                'prob' => $numSamples > 0 ? ($tinggiCount / $numSamples) : 0,
            ];
        }

        // Randomly select subset of features (Feature Bagging)
        $shuffledKeys = $featureKeys;
        shuffle($shuffledKeys);
        $subFeatures = array_slice($shuffledKeys, 0, $mFeatures);

        $bestGini = 1.0;
        $bestSplit = null;

        foreach ($subFeatures as $feat) {
            // Ambil unique threshold
            $values = array_column($samples, $feat);
            sort($values);
            $uniqueVals = array_unique($values);

            foreach ($uniqueVals as $val) {
                $leftSamples = [];
                $leftLabels = [];
                $rightSamples = [];
                $rightLabels = [];

                for ($i = 0; $i < $numSamples; $i++) {
                    if ($samples[$i][$feat] <= $val) {
                        $leftSamples[] = $samples[$i];
                        $leftLabels[] = $labels[$i];
                    } else {
                        $rightSamples[] = $samples[$i];
                        $rightLabels[] = $labels[$i];
                    }
                }

                if (empty($leftSamples) || empty($rightSamples)) continue;

                $gini = $this->calculateSplitGini($leftLabels, $rightLabels);
                if ($gini < $bestGini) {
                    $bestGini = $gini;
                    $bestSplit = [
                        'feature' => $feat,
                        'threshold' => $val,
                        'leftSamples' => $leftSamples,
                        'leftLabels' => $leftLabels,
                        'rightSamples' => $rightSamples,
                        'rightLabels' => $rightLabels,
                    ];
                }
            }
        }

        if (!$bestSplit) {
            return [
                'type' => 'leaf',
                'prediction' => ($tinggiCount >= $rendahCount) ? 'Tinggi' : 'Rendah',
                'prob' => $tinggiCount / ($numSamples ?: 1),
            ];
        }

        return [
            'type' => 'node',
            'feature' => $bestSplit['feature'],
            'threshold' => $bestSplit['threshold'],
            'left' => $this->buildTree($bestSplit['leftSamples'], $bestSplit['leftLabels'], $depth + 1, $featureKeys, $mFeatures),
            'right' => $this->buildTree($bestSplit['rightSamples'], $bestSplit['rightLabels'], $depth + 1, $featureKeys, $mFeatures),
        ];
    }

    protected function calculateSplitGini(array $leftLabels, array $rightLabels): float
    {
        $nL = count($leftLabels);
        $nR = count($rightLabels);
        $total = $nL + $nR;

        $giniL = $this->calculateGiniImpurity($leftLabels);
        $giniR = $this->calculateGiniImpurity($rightLabels);

        return (($nL / $total) * $giniL) + (($nR / $total) * $giniR);
    }

    protected function calculateGiniImpurity(array $labels): float
    {
        $n = count($labels);
        if ($n === 0) return 0.0;

        $t = 0;
        foreach ($labels as $l) {
            if ($l === 'Tinggi') $t++;
        }
        $p1 = $t / $n;
        $p2 = ($n - $t) / $n;

        return 1.0 - (pow($p1, 2) + pow($p2, 2));
    }

    protected function predictTree(array $node, array $sample): string
    {
        if ($node['type'] === 'leaf') {
            return $node['prediction'];
        }

        $feat = $node['feature'];
        $val = $sample[$feat] ?? 0;

        if ($val <= $node['threshold']) {
            return $this->predictTree($node['left'], $sample);
        } else {
            return $this->predictTree($node['right'], $sample);
        }
    }

    public function loadState(array $state): void
    {
        $this->trees = $state['trees'] ?? [];
        $this->numTrees = $state['num_trees'] ?? 25;
        $this->maxDepth = $state['max_depth'] ?? 6;
    }
}
