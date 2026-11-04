<?php

namespace App\Http\Controllers;

use App\Models\TindakanIntervensi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IntervensiController extends Controller
{
    public function index(Request $request)
    {
        $query = TindakanIntervensi::with(['siswa.kelas', 'user']);

        if ($request->filled('status_intervensi')) {
            $query->where('status_intervensi', $request->status_intervensi);
        }

        $intervensis = $query->latest('tgl_tindakan')->paginate(15)->withQueryString();
        $siswasBerisiko = Siswa::whereHas('latestPrediksi', function ($q) {
            $q->where('hasil_prediksi', 'Tinggi');
        })->orderBy('nama_lengkap')->get();

        return view('intervensi.index', compact('intervensis', 'siswasBerisiko'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'tgl_tindakan' => 'required|date',
            'jenis_tindakan' => 'required|string',
            'catatan_penanganan' => 'required|string',
            'status_intervensi' => 'required|in:Pending,Dalam Pemantauan,Selesai/Aman',
        ]);

        $validated['user_id'] = Auth::id();

        TindakanIntervensi::create($validated);

        return back()->with('success', 'Catatan tindakan intervensi berhasil disimpan.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_intervensi' => 'required|in:Pending,Dalam Pemantauan,Selesai/Aman',
        ]);

        $item = TindakanIntervensi::findOrFail($id);
        $item->update(['status_intervensi' => $request->status_intervensi]);

        return back()->with('success', 'Status intervensi berhasil diperbarui.');
    }
}
