<?php

namespace App\Http\Controllers;

use App\Models\RaporSemester;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\FiturDataset;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class NilaiPresensiController extends Controller
{
    public function index(Request $request)
    {
        $query = RaporSemester::with(['siswa.kelas']);

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        $rapors = $query->paginate(15)->withQueryString();
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return view('nilai.index', compact('rapors', 'kelasList'));
    }

    /**
     * Import Berkas Leger Nilai Rapor & Presensi (Excel)
     */
    public function importLeger(Request $request)
    {
        $request->validate([
            'file_leger' => 'required|file|mimes:xlsx,xls',
            'semester' => 'required|in:Ganjil,Genap',
            'tahun_ajaran' => 'required|string',
        ]);

        $file = $request->file('file_leger');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Cari header row (biasanya baris ke-5 yang ada NISN & kolom presensi S, I, A)
        $headerRowIdx = -1;
        foreach ($rows as $idx => $row) {
            foreach ($row as $cell) {
                if (is_string($cell) && strtolower(trim($cell)) === 'nisn') {
                    $headerRowIdx = $idx;
                    break 2;
                }
            }
        }

        if ($headerRowIdx === -1) {
            return back()->with('error', 'Format Leger tidak dikenali. Kolom NISN tidak ditemukan.');
        }

        $headers = array_map(fn($h) => strtoupper(trim((string)$h)), $rows[$headerRowIdx]);
        $nisnCol = array_search('NISN', $headers);
        $sCol = array_search('S', $headers);
        $iCol = array_search('I', $headers);
        $aCol = array_search('A', $headers);

        $imported = 0;
        for ($r = $headerRowIdx + 1; $r < count($rows); $r++) {
            $row = $rows[$r];
            $nisn = trim((string)($row[$nisnCol] ?? ''));
            if (empty($nisn)) continue;

            $siswa = Siswa::where('nisn', $nisn)->first();
            if (!$siswa) continue;

            // Hitung nilai mapel di antara NISN dan S/I/A
            $nilaiMapels = [];
            $endCol = ($sCol !== false) ? $sCol : count($row);
            for ($c = $nisnCol + 1; $c < $endCol; $c++) {
                $val = trim((string)($row[$c] ?? ''));
                if (is_numeric($val)) {
                    $nilaiMapels[] = (float)$val;
                }
            }

            $rerata = count($nilaiMapels) > 0 ? (array_sum($nilaiMapels) / count($nilaiMapels)) : 75.0;
            $dibawahKkm = 0;
            foreach ($nilaiMapels as $nm) {
                if ($nm < 75.0) $dibawahKkm++;
            }

            $sakit = ($sCol !== false && is_numeric($row[$sCol])) ? (int)$row[$sCol] : 0;
            $izin = ($iCol !== false && is_numeric($row[$iCol])) ? (int)$row[$iCol] : 0;
            $alfa = ($aCol !== false && is_numeric($row[$aCol])) ? (int)$row[$aCol] : 0;

            RaporSemester::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'semester' => $request->semester,
                    'tahun_ajaran' => $request->tahun_ajaran,
                ],
                [
                    'rerata_nilai' => round($rerata, 2),
                    'jml_mapel_dibawah_kkm' => $dibawahKkm,
                    'total_sakit' => $sakit,
                    'total_izin' => $izin,
                    'total_alfa' => $alfa,
                ]
            );

            // Sinkronkan ke FiturDataset KDD
            $kodeEkonomi = ($siswa->is_layak_pip || $siswa->is_penerima_kip) ? 1 : 2;
            $labelTarget = ($alfa >= 10 || ($alfa >= 5 && $rerata < 75)) ? 'Tinggi' : 'Rendah';

            FiturDataset::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'periode' => "{$request->tahun_ajaran}-{$request->semester}",
                ],
                [
                    'rerata_nilai' => round($rerata, 2),
                    'mapel_dibawah_kkm' => $dibawahKkm,
                    'jml_alfa' => $alfa,
                    'jml_izin_sakit' => $sakit + $izin,
                    'kode_ekonomi' => $kodeEkonomi,
                    'jarak_km' => $siswa->jarak_rumah_km,
                    'jml_saudara' => $siswa->jml_saudara_kandung,
                    'label_aktual' => $labelTarget,
                ]
            );

            $imported++;
        }

        return redirect()->route('nilai.index')->with('success', "Berhasil memproses {$imported} data nilai & presensi leger rapor.");
    }
}
