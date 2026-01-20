<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PokemonStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'pokemon_id',
        'stat_name',
        'base_stat',
        'effort',
    ];

    protected $casts = [
        'pokemon_id' => 'integer',
        'base_stat' => 'integer',
        'effort' => 'integer',
    ];

    public function pokemon(): BelongsTo
    {
        return $this->belongsTo(Pokemon::class);
    }
}
