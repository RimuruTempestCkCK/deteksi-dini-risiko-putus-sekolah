<?php

namespace App\Services\MachineLearning;

class NaiveBayesService
{
    protected array $classes = ['Rendah', 'Tinggi'];
    protected array $priors = [0.9694835680751174, 0.03051643192488263]; // 826/852 vs 26/852
    protected array $theta = []; // Mean (2 x 8)
    protected array $var = [];   // Variance (2 x 8)
    protected array $featureCols = [
        'X1_Nilai', 'X2_KKM', 'X3_Alfa', 'X4_IzinSakit',
        'X5_Ekonomi', 'X6_Jarak', 'X7_Pelanggaran', 'X8_Dukungan'
    ];

    public function __construct()
    {
        $this->loadStateFromStorage();
    }

    public function loadState(array $state): void
    {
        if (isset($state['class_prior'])) {
            $this->priors = $state['class_prior'];
        }
        if (isset($state['theta'])) {
            $this->theta = $state['theta'];
        }
        if (isset($state['var'])) {
            $this->var = $state['var'];
        }
        if (isset($state['feature_cols'])) {
            $this->featureCols = $state['feature_cols'];
        }
    }

    public function loadStateFromStorage(): bool
    {
        $path = storage_path('app/nb_model_state.json');
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

    public function getParameters(): array
    {
        return [
            'priors' => $this->priors,
            'theta' => $this->theta,
            'var' => $this->var,
            'features' => $this->featureCols
        ];
    }

    /**
     * Hitung Gaussian Probability Density Function:
     * P(x | mean, var) = (1 / sqrt(2 * pi * var)) * exp(-((x - mean)^2) / (2 * var))
     */
    public function calcGaussian(float $x, float $mean, float $var): float
    {
        if ($var <= 0) $var = 1e-9;
        $coeff = 1.0 / sqrt(2.0 * M_PI * $var);
        $exponent = -pow($x - $mean, 2) / (2.0 * $var);
        return $coeff * exp($exponent);
    }

    /**
     * Prediksi satu sampel menggunakan Gaussian Naïve Bayes
     */
    public function predict(array $sample): array
    {
        if (empty($this->theta) || empty($this->var)) {
            $this->loadStateFromStorage();
        }

        $xVec = [];
        if (isset($sample['X1_Nilai'])) {
            foreach ($this->featureCols as $fc) {
                $xVec[] = (float) ($sample[$fc] ?? 0.0);
            }
        } else {
            $xVec = array_map('floatval', array_values($sample));
        }

        // Hitung log joint probabilities untuk kestabilan numerik persis scikit-learn
        $jointLogs = [];
        $likelihoods = [[], []];

        for ($c = 0; $c < 2; $c++) {
            $logProbSum = 0.0;
            for ($f = 0; $f < count($this->featureCols); $f++) {
                $xVal = $xVec[$f];
                $mean = (float) $this->theta[$c][$f];
                $variance = (float) $this->var[$c][$f];

                $l = $this->calcGaussian($xVal, $mean, $variance);
                $likelihoods[$c][$this->featureCols[$f]] = $l;

                // Log-term: -0.5 * (log(2 * pi * var) + ((x - mean)^2) / var)
                $logLikelihood = -0.5 * (log(2.0 * M_PI * $variance) + (pow($xVal - $mean, 2) / $variance));
                $logProbSum += $logLikelihood;
            }
            $jointLogs[$c] = log($this->priors[$c]) + $logProbSum;
        }

        // Softmax normalization
        $maxLog = max($jointLogs);
        $exp0 = exp($jointLogs[0] - $maxLog);
        $exp1 = exp($jointLogs[1] - $maxLog);
        $sumExp = $exp0 + $exp1;

        $post0 = $exp0 / $sumExp;
        $post1 = $exp1 / $sumExp;

        $label = ($post1 > 0.5) ? 'Tinggi' : 'Rendah';

        return [
            'label' => $label,
            'probabilitas' => round($post1, 4),
            'probabilitas_tinggi' => round($post1, 6),
            'probabilitas_rendah' => round($post0, 6),
            'percent_tinggi' => round($post1 * 100, 2),
            'percent_rendah' => round($post0 * 100, 2),
            'likelihoods' => $likelihoods,
            'joint_logs' => $jointLogs,
        ];
    }
}
