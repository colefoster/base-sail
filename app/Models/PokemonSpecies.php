<?php

namespace App\Models;

use App\Models\Traits\FormatsNameAttribute;
use App\Models\Traits\HasApiRouteKey;
use App\Models\Traits\OrderedByApiId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PokemonSpecies extends Model
{
    use FormatsNameAttribute, HasApiRouteKey, HasFactory, OrderedByApiId;

    protected $fillable = [
        'api_id',
        'name',
        'base_happiness',
        'capture_rate',
        'color',
        'gender_rate',
        'hatch_counter',
        'is_baby',
        'is_legendary',
        'is_mythical',
        'habitat',
        'shape',
        'generation',
        'evolution_chain_id',
    ];

    protected $casts = [
        'api_id' => 'integer',
        'base_happiness' => 'integer',
        'capture_rate' => 'integer',
        'gender_rate' => 'integer',
        'hatch_counter' => 'integer',
        'is_baby' => 'boolean',
        'is_legendary' => 'boolean',
        'is_mythical' => 'boolean',
        'evolution_chain_id' => 'integer',
    ];

    public function evolutionChain(): BelongsTo
    {
        return $this->belongsTo(EvolutionChain::class);
    }

    public function pokemon(): HasMany
    {
        return $this->hasMany(Pokemon::class, 'species_id');
    }

    public function evolutions(): HasMany
    {
        return $this->hasMany(Evolution::class, 'species_id');
    }

    public function evolutionsTo(): HasMany
    {
        return $this->hasMany(Evolution::class, 'evolves_to_species_id');
    }
}
