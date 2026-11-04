<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelEvaluasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'algoritma',
        'akurasi',
        'presisi',
        'recall',
        'f1_score',
        'confusion_matrix',
        'hyperparameter_settings',
        'model_state',
        'is_active',
    ];

    protected $casts = [
        'akurasi' => 'decimal:2',
        'presisi' => 'decimal:2',
        'recall' => 'decimal:2',
        'f1_score' => 'decimal:2',
        'confusion_matrix' => 'array',
        'hyperparameter_settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function prediksiLogs()
    {
        return $this->hasMany(PrediksiLog::class, 'model_evaluasi_id');
    }
}
