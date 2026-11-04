<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiturDataset extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'periode',
        'rerata_nilai',
        'mapel_dibawah_kkm',
        'jml_alfa',
        'jml_izin_sakit',
        'kode_ekonomi',
        'jarak_km',
        'jml_saudara',
        'label_aktual',
    ];

    protected $casts = [
        'rerata_nilai' => 'decimal:2',
        'jarak_km' => 'decimal:2',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
