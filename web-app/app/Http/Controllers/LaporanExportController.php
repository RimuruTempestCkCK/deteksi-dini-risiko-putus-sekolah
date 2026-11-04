<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\ModelEvaluasi;
use App\Models\TindakanIntervensi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanExportController extends Controller
{
    public function index()
    {
        $activeModel = ModelEvaluasi::where('is_active', true)->first();
        $siswas = Siswa::with(['kelas', 'latestPrediksi', 'tindakanIntervensis'])->get();

        $tinggiCount = $siswas->filter(fn($s) => $s->latestPrediksi && $s->latestPrediksi->hasil_prediksi === 'Tinggi')->count();
        $rendahCount = $siswas->filter(fn($s) => $s->latestPrediksi && $s->latestPrediksi->hasil_prediksi === 'Rendah')->count();

        return view('laporan.index', compact('activeModel', 'siswas', 'tinggiCount', 'rendahCount'));
    }

    public function downloadPdf(Request $request)
    {
        $filterRisiko = $request->input('risiko');
        $query = Siswa::with(['kelas', 'latestPrediksi']);

        if ($filterRisiko) {
            $query->whereHas('latestPrediksi', function ($q) use ($filterRisiko) {
                $q->where('hasil_prediksi', $filterRisiko);
            });
        }

        $siswas = $query->orderBy('nama_lengkap')->get();
        $activeModel = ModelEvaluasi::where('is_active', true)->first();

        $pdf = Pdf::loadView('laporan.pdf', compact('siswas', 'activeModel', 'filterRisiko'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Deteksi_Dini_Risiko_Putus_Sekolah_SMKN1_Bungo.pdf');
    }
}
