<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvolutionChain extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_id',
        'baby_trigger_item',
    ];

    protected $casts = [
        'api_id' => 'integer',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', function ($query) {
            $query->orderBy('api_id');
        });
    }

    public function species(): HasMany
    {
        return $this->hasMany(PokemonSpecies::class);
    }

    public function evolutions(): HasMany
    {
        return $this->hasMany(Evolution::class);
    }
}