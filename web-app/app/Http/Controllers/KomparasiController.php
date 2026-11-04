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

        // Tentukan model pemenang
        $winner = 'Random_Forest';
        if ($rfModel && $nbModel) {
            if ($nbModel->f1_score > $rfModel->f1_score) {
                $winner = 'Naive_Bayes';
            }
        }

        return view('komparasi.index', compact('rfModel', 'nbModel', 'winner'));
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
