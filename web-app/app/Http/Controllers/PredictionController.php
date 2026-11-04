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
        if (!$activeModel || empty($activeModel->model_state)) {
            return back()->with('error', 'Belum ada Model Klasifikasi Aktif yang dilatih. Silakan jalankan training KDD terlebih dahulu.');
        }

        $prepService = new PreprocessingService();
        $state = json_decode($activeModel->model_state, true);

        if ($activeModel->algoritma === 'Random_Forest') {
            $mlService = new RandomForestService();
            $mlService->loadState($state);
        } else {
            $mlService = new NaiveBayesService();
            $mlService->loadState($state);
        }

        $siswas = Siswa::with('raporSemesters')->get();
        $count = 0;

        foreach ($siswas as $s) {
            $features = $prepService->extractFeaturesFromSiswa($s);
            $pred = $mlService->predict($features);
            $factors = $prepService->identifyRiskFactors($features);

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
            $count++;
        }

        return redirect()->route('prediksi.index')->with('success', "Proses batch klasifikasi selesai! {$count} data siswa berhasil diprediksi menggunakan model {$activeModel->algoritma}.");
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
            'rerata_nilai' => 'required|numeric|min:0|max:100',
            'mapel_dibawah_kkm' => 'required|integer|min:0',
            'jml_alfa' => 'required|integer|min:0',
            'jml_izin_sakit' => 'required|integer|min:0',
            'kode_ekonomi' => 'required|in:1,2,3',
            'jarak_km' => 'required|numeric|min:0',
            'jml_saudara' => 'required|integer|min:0',
        ]);

        $activeModel = ModelEvaluasi::where('is_active', true)->first();
        if (!$activeModel) {
            return back()->with('error', 'Model aktif belum tersedia.');
        }

        $features = [
            'rerata_nilai' => (float) $request->rerata_nilai,
            'mapel_dibawah_kkm' => (int) $request->mapel_dibawah_kkm,
            'jml_alfa' => (int) $request->jml_alfa,
            'jml_izin_sakit' => (int) $request->jml_izin_sakit,
            'kode_ekonomi' => (int) $request->kode_ekonomi,
            'jarak_km' => (float) $request->jarak_km,
            'jml_saudara' => (int) $request->jml_saudara,
        ];

        $prepService = new PreprocessingService();
        $state = json_decode($activeModel->model_state, true);

        if ($activeModel->algoritma === 'Random_Forest') {
            $ml = new RandomForestService();
            $ml->loadState($state);
        } else {
            $ml = new NaiveBayesService();
            $ml->loadState($state);
        }

        $result = $ml->predict($features);
        $factors = $prepService->identifyRiskFactors($features);

        return back()->with('simulasi_result', [
            'label' => $result['label'],
            'probabilitas' => $result['probabilitas'],
            'faktor_kunci' => $factors,
            'input' => $features,
            'model' => $activeModel->algoritma
        ]);
    }
}
