<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'kelas_id',
        'nisn',
        'nipd',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'alat_transportasi',
        'is_penerima_kps',
        'is_penerima_kip',
        'is_penerima_kks',
        'is_layak_pip',
        'pekerjaan_ayah',
        'pekerjaan_ibu',
        'penghasilan_ayah',
        'penghasilan_ibu',
        'jml_saudara_kandung',
        'jarak_rumah_km',
        'status_keaktifan',
    ];

    protected $casts = [
        'is_penerima_kps' => 'boolean',
        'is_penerima_kip' => 'boolean',
        'is_penerima_kks' => 'boolean',
        'is_layak_pip' => 'boolean',
        'penghasilan_ayah' => 'decimal:2',
        'penghasilan_ibu' => 'decimal:2',
        'jarak_rumah_km' => 'decimal:2',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function raporSemesters()
    {
        return $this->hasMany(RaporSemester::class, 'siswa_id');
    }

    public function fiturDatasets()
    {
        return $this->hasMany(FiturDataset::class, 'siswa_id');
    }

    public function prediksiLogs()
    {
        return $this->hasMany(PrediksiLog::class, 'siswa_id');
    }

    public function tindakanIntervensis()
    {
        return $this->hasMany(TindakanIntervensi::class, 'siswa_id');
    }

    public function latestPrediksi()
    {
        return $this->hasOne(PrediksiLog::class, 'siswa_id')->latestOfMany();
    }
}
