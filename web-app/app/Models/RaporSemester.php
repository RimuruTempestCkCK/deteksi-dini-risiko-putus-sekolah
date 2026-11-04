<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaporSemester extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'semester',
        'tahun_ajaran',
        'rerata_nilai',
        'jml_mapel_dibawah_kkm',
        'total_sakit',
        'total_izin',
        'total_alfa',
    ];

    protected $casts = [
        'rerata_nilai' => 'decimal:2',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
