<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrediksiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'model_evaluasi_id',
        'hasil_prediksi',
        'probabilitas_risiko',
        'faktor_kunci',
        'tanggal_prediksi',
    ];

    protected $casts = [
        'probabilitas_risiko' => 'decimal:4',
        'tanggal_prediksi' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function modelEvaluasi()
    {
        return $this->belongsTo(ModelEvaluasi::class, 'model_evaluasi_id');
    }
}
