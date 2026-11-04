<?php

namespace App\Services\MachineLearning;

class NaiveBayesService
{
    protected array $classes = ['Tinggi', 'Rendah'];
    protected array $priors = [];
    protected array $stats = []; // mean & variance per feature per class

    /**
     * Melatih model Gaussian Naive Bayes berdasarkan dataset
     * $samples: array of [feature_key => value]
     * $labels: array of 'Tinggi' / 'Rendah'
     */
    public function train(array $samples, array $labels): array
    {
        $totalSamples = count($samples);
        if ($totalSamples === 0) {
            return [];
        }

        $featureKeys = array_keys($samples[0]);
        $separated = ['Tinggi' => [], 'Rendah' => []];

        for ($i = 0; $i < $totalSamples; $i++) {
            $label = $labels[$i];
            $separated[$label][] = $samples[$i];
        }

        // 1. Prior Probability P(C)
        foreach ($this->classes as $c) {
            $count = count($separated[$c]);
            $this->priors[$c] = $count / ($totalSamples ?: 1);
        }

        // 2. Mean & Variance untuk Likelihood Gaussian
        $this->stats = [];
        foreach ($this->classes as $c) {
            $this->stats[$c] = [];
            $classSamples = $separated[$c];
            $n = count($classSamples);

            foreach ($featureKeys as $key) {
                if ($n === 0) {
                    $this->stats[$c][$key] = ['mean' => 0, 'var' => 1e-4];
                    continue;
                }

                $sum = 0;
                foreach ($classSamples as $s) {
                    $sum += (float) ($s[$key] ?? 0);
                }
                $mean = $sum / $n;

                $varSum = 0;
                foreach ($classSamples as $s) {
                    $varSum += pow(((float) ($s[$key] ?? 0)) - $mean, 2);
                }
                // Laplacian / smoothing variance to avoid divide-by-zero
                $variance = ($varSum / ($n ?: 1)) + 1e-4;

                $this->stats[$c][$key] = [
                    'mean' => $mean,
                    'var' => $variance
                ];
            }
        }

        return [
            'priors' => $this->priors,
            'stats' => $this->stats,
        ];
    }

    /**
     * Prediksi satu data sampel
     */
    public function predict(array $sample): array
    {
        $posteriors = [];

        foreach ($this->classes as $c) {
            // Gunakan log-likelihood untuk kestabilan numerik
            $logProb = log($this->priors[$c] > 0 ? $this->priors[$c] : 1e-6);

            foreach ($sample as $key => $val) {
                if (!isset($this->stats[$c][$key])) continue;

                $mean = $this->stats[$c][$key]['mean'];
                $var = $this->stats[$c][$key]['var'];

                // Gaussian probability density function:
                // P(x) = (1 / sqrt(2*pi*var)) * exp(-((x-mean)^2)/(2*var))
                $exponent = -pow($val - $mean, 2) / (2 * $var);
                $coeff = 1.0 / sqrt(2 * M_PI * $var);
                $prob = $coeff * exp($exponent);

                if ($prob <= 0) $prob = 1e-9;
                $logProb += log($prob);
            }

            $posteriors[$c] = $logProb;
        }

        // Konversi log posterior ke normal probability distribution (Softmax)
        $maxLog = max($posteriors['Tinggi'], $posteriors['Rendah']);
        $expTinggi = exp($posteriors['Tinggi'] - $maxLog);
        $expRendah = exp($posteriors['Rendah'] - $maxLog);
        $totalExp = $expTinggi + $expRendah;

        $probTinggi = $totalExp > 0 ? ($expTinggi / $totalExp) : 0.5;
        $label = ($probTinggi >= 0.5) ? 'Tinggi' : 'Rendah';

        return [
            'label' => $label,
            'probabilitas' => round($probTinggi, 4),
        ];
    }

    public function loadState(array $state): void
    {
        $this->priors = $state['priors'] ?? [];
        $this->stats = $state['stats'] ?? [];
    }
}
