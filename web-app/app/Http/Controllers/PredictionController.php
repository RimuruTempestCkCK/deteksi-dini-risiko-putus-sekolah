<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\ModelEvaluasi;
use App\Models\PrediksiLog;
use App\Services\MachineLearning\RandomForestService;
use App\Services\MachineLearning\NaiveBayesService;
use App\Services\MachineLearning\PreprocessingService;
use Illuminate\Http\Request;

class PredictionController extends Controller
{
    public function index(Request $request)
    {
        $query = PrediksiLog::with(['siswa.kelas', 'modelEvaluasi']);

        if ($request->filled('hasil_prediksi')) {
            $query->where('hasil_prediksi', $request->hasil_prediksi);
        }

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        $prediksis = $query->latest('id')->paginate(15)->withQueryString();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $activeModel = ModelEvaluasi::where('is_active', true)->first();

        return view('prediksi.index', compact('prediksis', 'kelasList', 'activeModel'));
    }

    /**
     * Batch Prediction untuk seluruh siswa yang belum/sudah diprediksi
     */
    public function runBatch()
    {
        $activeModel = ModelEvaluasi::where('is_active', true)->first();
        $prepService = new PreprocessingService();

        $rfService = new RandomForestService();
        $nbService = new NaiveBayesService();

        $useRf = (!$activeModel || $activeModel->algoritma === 'Random_Forest');
        $mlService = $useRf ? $rfService : $nbService;

        $siswas = Siswa::with('raporSemesters')->get();
        $count = 0;

        foreach ($siswas as $s) {
            $features = $prepService->extractFeaturesFromSiswa($s);
            $pred = $mlService->predict($features);
            $factors = $prepService->identifyRiskFactors($features);

            if ($activeModel) {
                PrediksiLog::updateOrCreate(
                    [
                        'siswa_id' => $s->id,
                        'model_evaluasi_id' => $activeModel->id,
                        'tanggal_prediksi' => now()->toDateString(),
                    ],
                    [
                        'hasil_prediksi' => $pred['label'],
                        'probabilitas_risiko' => $pred['probabilitas'],
                        'faktor_kunci' => $factors,
                    ]
                );
            }
            $count++;
        }

        $algoName = $activeModel ? $activeModel->algoritma : 'Random Forest';
        return redirect()->route('prediksi.index')->with('success', "Proses batch klasifikasi selesai! {$count} data siswa berhasil diprediksi menggunakan model {$algoName}.");
    }

    /**
     * Simulasi Manual / Prediksi Single
     */
    public function formSimulasi()
    {
        $activeModel = ModelEvaluasi::where('is_active', true)->first();
        return view('prediksi.simulasi', compact('activeModel'));
    }

    public function processSimulasi(Request $request)
    {
        $request->validate([
            'X1_Nilai' => 'nullable|numeric|min:0|max:100',
            'rerata_nilai' => 'nullable|numeric|min:0|max:100',
            'X2_KKM' => 'nullable|integer|min:0',
            'mapel_dibawah_kkm' => 'nullable|integer|min:0',
            'X3_Alfa' => 'nullable|integer|min:0',
            'jml_alfa' => 'nullable|integer|min:0',
            'X4_IzinSakit' => 'nullable|integer|min:0',
            'jml_izin_sakit' => 'nullable|integer|min:0',
            'X5_Ekonomi' => 'nullable|in:1,2,3',
            'kode_ekonomi' => 'nullable|in:1,2,3',
            'X6_Jarak' => 'nullable|numeric|min:0',
            'jarak_km' => 'nullable|numeric|min:0',
            'X7_Pelanggaran' => 'nullable|integer|min:0',
            'jml_pelanggaran' => 'nullable|integer|min:0',
            'X8_Dukungan' => 'nullable|in:1,2,3',
            'skor_dukungan' => 'nullable|in:1,2,3',
        ]);

        $activeModel = ModelEvaluasi::where('is_active', true)->first();

        // Ambil nilai 8 fitur prediktor KDD penelitian
        $x1 = (float) ($request->X1_Nilai ?? $request->rerata_nilai ?? 79.49);
        $x2 = (int) ($request->X2_KKM ?? $request->mapel_dibawah_kkm ?? 1);
        $x3 = (int) ($request->X3_Alfa ?? $request->jml_alfa ?? 7);
        $x4 = (int) ($request->X4_IzinSakit ?? $request->jml_izin_sakit ?? 4);
        $x5 = (int) ($request->X5_Ekonomi ?? $request->kode_ekonomi ?? 2);
        $x6 = (float) ($request->X6_Jarak ?? $request->jarak_km ?? 15.0);
        $x7 = (int) ($request->X7_Pelanggaran ?? $request->jml_pelanggaran ?? 1);
        $x8 = (int) ($request->X8_Dukungan ?? $request->skor_dukungan ?? 3);

        $features = [
            'X1_Nilai' => round($x1, 2),
            'X2_KKM' => $x2,
            'X3_Alfa' => $x3,
            'X4_IzinSakit' => $x4,
            'X5_Ekonomi' => $x5,
            'X6_Jarak' => round($x6, 1),
            'X7_Pelanggaran' => $x7,
            'X8_Dukungan' => $x8,
        ];

        $prepService = new PreprocessingService();
        $rf = new RandomForestService();
        $nb = new NaiveBayesService();

        $resRf = $rf->predict($features);
        $resNb = $nb->predict($features);
        $factors = $prepService->identifyRiskFactors($features);

        $selectedAlgo = $activeModel ? $activeModel->algoritma : 'Random_Forest';
        $mainResult = ($selectedAlgo === 'Naive_Bayes') ? $resNb : $resRf;

        return back()->with('simulasi_result', [
            'label' => $mainResult['label'],
            'probabilitas' => $mainResult['probabilitas'],
            'probabilitas_tinggi' => $mainResult['probabilitas_tinggi'],
            'probabilitas_rendah' => $mainResult['probabilitas_rendah'],
            'rf_label' => $resRf['label'],
            'rf_prob' => $resRf['probabilitas_tinggi'],
            'nb_label' => $resNb['label'],
            'nb_prob' => $resNb['probabilitas_tinggi'],
            'faktor_kunci' => $factors,
            'input' => $features,
            'model' => $selectedAlgo
        ]);
    }
}
