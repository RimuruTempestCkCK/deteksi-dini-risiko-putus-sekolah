<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\RaporSemester;
use App\Models\FiturDataset;
use App\Services\MachineLearning\PreprocessingService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::with(['kelas', 'latestPrediksi']);

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('jurusan')) {
            $query->whereHas('kelas', function ($q) use ($request) {
                $q->where('jurusan', $request->jurusan);
            });
        }

        if ($request->filled('status_risiko')) {
            $query->whereHas('latestPrediksi', function ($q) use ($request) {
                $q->where('hasil_prediksi', $request->status_risiko);
            });
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_lengkap', 'like', "%{$s}%")
                  ->orWhere('nisn', 'like', "%{$s}%")
                  ->orWhere('nipd', 'like', "%{$s}%");
            });
        }

        $siswas = $query->paginate(15)->withQueryString();
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $jurusanList = Kelas::select('jurusan')->distinct()->pluck('jurusan');

        return view('siswa.index', compact('siswas', 'kelasList', 'jurusanList'));
    }

    public function show($id)
    {
        $siswa = Siswa::with(['kelas', 'raporSemesters', 'prediksiLogs.modelEvaluasi', 'tindakanIntervensis.user'])->findOrFail($id);
        return view('siswa.show', compact('siswa'));
    }

    public function create()
    {
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        return view('siswa.create', compact('kelasList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn' => 'required|unique:siswas,nisn',
            'nipd' => 'nullable|string',
            'nama_lengkap' => 'required|string',
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'alat_transportasi' => 'nullable|string',
            'penghasilan_ayah' => 'nullable|numeric',
            'penghasilan_ibu' => 'nullable|numeric',
            'jml_saudara_kandung' => 'nullable|integer',
            'jarak_rumah_km' => 'nullable|numeric',
        ]);

        $validated['is_penerima_kps'] = $request->boolean('is_penerima_kps');
        $validated['is_penerima_kip'] = $request->boolean('is_penerima_kip');
        $validated['is_penerima_kks'] = $request->boolean('is_penerima_kks');
        $validated['is_layak_pip'] = $request->boolean('is_layak_pip');

        Siswa::create($validated);

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        $kelasList = Kelas::orderBy('nama_kelas')->get();
        return view('siswa.edit', compact('siswa', 'kelasList'));
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $validated = $request->validate([
            'nisn' => 'required|unique:siswas,nisn,' . $siswa->id,
            'nipd' => 'nullable|string',
            'nama_lengkap' => 'required|string',
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'alat_transportasi' => 'nullable|string',
            'penghasilan_ayah' => 'nullable|numeric',
            'penghasilan_ibu' => 'nullable|numeric',
            'jml_saudara_kandung' => 'nullable|integer',
            'jarak_rumah_km' => 'nullable|numeric',
            'status_keaktifan' => 'required|in:Aktif,Mutasi,Putus Sekolah,Lulus',
        ]);

        $validated['is_penerima_kps'] = $request->boolean('is_penerima_kps');
        $validated['is_penerima_kip'] = $request->boolean('is_penerima_kip');
        $validated['is_penerima_kks'] = $request->boolean('is_penerima_kks');
        $validated['is_layak_pip'] = $request->boolean('is_layak_pip');

        $siswa->update($validated);

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    /**
     * Import Berkas Excel Dapodik (Daftar PD)
     */
    public function importDapodik(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls',
        ]);

        $file = $request->file('file_excel');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Cari header row (biasanya pada baris ke-5 yang memiliki kolom NISN)
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
            return back()->with('error', 'Format file tidak dikenali. Kolom NISN tidak ditemukan.');
        }

        $headers = array_map(fn($h) => strtolower(trim((string)$h)), $rows[$headerRowIdx]);
        $nisnCol = array_search('nisn', $headers);
        $namaCol = array_search('nama', $headers);
        $nipdCol = array_search('nipd', $headers);
        $jkCol = array_search('jk', $headers);
        $rombelCol = array_search('rombel saat ini', $headers);
        $kipCol = array_search('penerima kip', $headers);
        $kpsCol = array_search('penerima kps', $headers);
        $pipCol = array_search('layak pip (usulan dari sekolah)', $headers);
        $jarakCol = array_search('jarak rumah ke sekolah (km)', $headers);

        $imported = 0;
        for ($i = $headerRowIdx + 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $nisn = trim((string)($row[$nisnCol] ?? ''));
            $nama = trim((string)($row[$namaCol] ?? ''));

            if (empty($nisn) || empty($nama)) continue;

            $rombelName = $rombelCol !== false ? trim((string)($row[$rombelCol] ?? '')) : '';
            $kelasId = null;
            if ($rombelName) {
                $kelas = Kelas::firstOrCreate(
                    ['nama_kelas' => $rombelName, 'tahun_ajaran' => '2023/2024'],
                    ['tingkat' => 'X', 'jurusan' => 'Umum']
                );
                $kelasId = $kelas->id;
            }

            Siswa::updateOrCreate(
                ['nisn' => $nisn],
                [
                    'kelas_id' => $kelasId,
                    'nipd' => $nipdCol !== false ? trim((string)($row[$nipdCol] ?? '')) : null,
                    'nama_lengkap' => $nama,
                    'jenis_kelamin' => ($jkCol !== false && strtoupper(trim((string)($row[$jkCol] ?? ''))) === 'P') ? 'P' : 'L',
                    'is_penerima_kip' => ($kipCol !== false && strtolower(trim((string)($row[$kipCol] ?? ''))) === 'ya'),
                    'is_penerima_kps' => ($kpsCol !== false && strtolower(trim((string)($row[$kpsCol] ?? ''))) === 'ya'),
                    'is_layak_pip' => ($pipCol !== false && strtolower(trim((string)($row[$pipCol] ?? ''))) === 'ya'),
                    'jarak_rumah_km' => $jarakCol !== false ? (float)($row[$jarakCol] ?? 0) : 0,
                    'status_keaktifan' => 'Aktif',
                ]
            );
            $imported++;
        }

        return redirect()->route('siswa.index')->with('success', "Berhasil mengimpor {$imported} data siswa dari Dapodik.");
    }
}
