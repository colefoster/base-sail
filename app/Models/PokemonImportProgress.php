<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PokemonImportProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'import_id',
        'status',
        'current_step',
        'current_step_index',
        'total_steps',
        'current_step_processed',
        'current_step_total',
        'step_details',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'current_step_index' => 'integer',
        'total_steps' => 'integer',
        'current_step_processed' => 'integer',
        'current_step_total' => 'integer',
        'step_details' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}