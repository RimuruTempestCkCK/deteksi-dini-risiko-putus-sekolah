<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TindakanIntervensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'user_id',
        'tgl_tindakan',
        'jenis_tindakan',
        'catatan_penanganan',
        'status_intervensi',
    ];

    protected $casts = [
        'tgl_tindakan' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
