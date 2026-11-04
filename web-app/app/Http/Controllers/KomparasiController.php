<?php

namespace App\Http\Controllers;

use App\Models\ModelEvaluasi;
use Illuminate\Http\Request;

class KomparasiController extends Controller
{
    public function index()
    {
        $rfModel = ModelEvaluasi::where('algoritma', 'Random_Forest')->first();
        $nbModel = ModelEvaluasi::where('algoritma', 'Naive_Bayes')->first();

        // Tentukan model pemenang berdasarkan evaluasi ilmiah
        $winner = 'Random_Forest';
        $alasanRekomendasi = '';

        if ($rfModel && $nbModel) {
            if ($rfModel->f1_score > $nbModel->f1_score) {
                $winner = 'Random_Forest';
                $alasanRekomendasi = "Random Forest unggul dengan nilai F1-Score ({$rfModel->f1_score}%) lebih tinggi dibandingkan Naïve Bayes ({$nbModel->f1_score}%).";
            } elseif ($nbModel->f1_score > $rfModel->f1_score) {
                $winner = 'Naive_Bayes';
                $alasanRekomendasi = "Naïve Bayes unggul dengan nilai F1-Score ({$nbModel->f1_score}%) lebih tinggi dibandingkan Random Forest ({$rfModel->f1_score}%).";
            } elseif ($rfModel->akurasi > $nbModel->akurasi) {
                $winner = 'Random_Forest';
                $alasanRekomendasi = "Random Forest unggul dalam persentase Akurasi klasifikasi ({$rfModel->akurasi}% vs {$nbModel->akurasi}%).";
            } elseif ($nbModel->akurasi > $rfModel->akurasi) {
                $winner = 'Naive_Bayes';
                $alasanRekomendasi = "Naïve Bayes unggul dalam persentase Akurasi klasifikasi ({$nbModel->akurasi}% vs {$rfModel->akurasi}%).";
            } else {
                // Ketika kedua nilai metrik sama (misal sama-sama 100% pada data uji)
                $winner = 'Random_Forest';
                $alasanRekomendasi = "Kedua model memperoleh nilai metrik yang sama (Akurasi & F1-Score 100% pada data uji 20%). Random Forest dipilih sebagai rekomendasi utama karena menggunakan pendekatan Ensemble Learning (30 Decision Trees & Majority Voting) yang terbukti secara teoritis lebih stabil terhadap multivariat dan resisten terhadap overfitting dibandingkan asumsi independensi fitur Gaussian Naïve Bayes.";
            }
        }

        return view('komparasi.index', compact('rfModel', 'nbModel', 'winner', 'alasanRekomendasi'));
    }

    /**
     * Set salah satu model sebagai mesin inferensi aktif
     */
    public function setActive(Request $request)
    {
        $request->validate([
            'algoritma' => 'required|in:Random_Forest,Naive_Bayes',
        ]);

        ModelEvaluasi::query()->update(['is_active' => false]);
        ModelEvaluasi::where('algoritma', $request->algoritma)->update(['is_active' => true]);

        $namaModel = $request->algoritma === 'Random_Forest' ? 'Random Forest' : 'Naïve Bayes';
        return back()->with('success', "Model {$namaModel} berhasil diatur sebagai Model Inferensi Aktif sistem!");
    }
}
