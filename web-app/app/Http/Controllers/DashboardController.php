<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\PrediksiLog;
use App\Models\ModelEvaluasi;
use App\Models\TindakanIntervensi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSiswa = Siswa::count();
        $totalKelas = Kelas::count();
        
        $activeModel = ModelEvaluasi::where('is_active', true)->first();

        // Statistik Prediksi Terkini
        $highRiskCount = PrediksiLog::where('hasil_prediksi', 'Tinggi')->count();
        $lowRiskCount = PrediksiLog::where('hasil_prediksi', 'Rendah')->count();
        $totalPrediksi = $highRiskCount + $lowRiskCount;

        // Distribusi Risiko per Jurusan
        $jurusanStats = Kelas::with(['siswas.latestPrediksi'])
            ->get()
            ->groupBy('jurusan')
            ->map(function ($items, $jurusan) {
                $tinggi = 0;
                $rendah = 0;
                foreach ($items as $k) {
                    foreach ($k->siswas as $s) {
                        if ($s->latestPrediksi) {
                            if ($s->latestPrediksi->hasil_prediksi === 'Tinggi') $tinggi++;
                            else $rendah++;
                        }
                    }
                }
                return [
                    'jurusan' => $jurusan,
                    'tinggi' => $tinggi,
                    'rendah' => $rendah,
                    'total' => $tinggi + $rendah,
                ];
            })->values();

        // 5 Siswa dengan Prioritas Intervensi Tertinggi (Probabilitas Risiko Tertinggi)
        $urgentStudents = Siswa::with(['kelas', 'latestPrediksi', 'tindakanIntervensis'])
            ->whereHas('latestPrediksi', function ($q) {
                $q->where('hasil_prediksi', 'Tinggi');
            })
            ->get()
            ->sortByDesc(function ($s) {
                return $s->latestPrediksi->probabilitas_risiko ?? 0;
            })
            ->take(5);

        // Rekap Tindakan Intervensi
        $intervensiPending = TindakanIntervensi::where('status_intervensi', 'Pending')->count();
        $intervensiMonitoring = TindakanIntervensi::where('status_intervensi', 'Dalam Pemantauan')->count();
        $intervensiSelesai = TindakanIntervensi::where('status_intervensi', 'Selesai/Aman')->count();

        return view('dashboard.index', compact(
            'totalSiswa',
            'totalKelas',
            'activeModel',
            'highRiskCount',
            'lowRiskCount',
            'totalPrediksi',
            'jurusanStats',
            'urgentStudents',
            'intervensiPending',
            'intervensiMonitoring',
            'intervensiSelesai'
        ));
    }
}
