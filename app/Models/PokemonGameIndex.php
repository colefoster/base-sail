<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PokemonGameIndex extends Model
{
    use HasFactory;

    protected $fillable = [
        'pokemon_id',
        'game_index',
        'version',
    ];

    protected $casts = [
        'pokemon_id' => 'integer',
        'game_index' => 'integer',
    ];

    public function pokemon(): BelongsTo
    {
        return $this->belongsTo(Pokemon::class);
    }
}
