<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evolution extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'evolution_chain_id',
        'species_id',
        'evolves_to_species_id',
        'trigger',
        'min_level',
        'item',
        'held_item',
        'gender',
        'min_happiness',
        'min_beauty',
        'min_affection',
        'location',
        'time_of_day',
        'known_move',
        'known_move_type',
        'party_species',
        'party_type',
        'relative_physical_stats',
        'needs_overworld_rain',
        'trade_species',
        'turn_upside_down',
    ];

    protected $casts = [
        'evolution_chain_id' => 'integer',
        'species_id' => 'integer',
        'evolves_to_species_id' => 'integer',
        'min_level' => 'integer',
        'min_happiness' => 'integer',
        'min_beauty' => 'integer',
        'min_affection' => 'integer',
        'relative_physical_stats' => 'integer',
        'needs_overworld_rain' => 'boolean',
        'turn_upside_down' => 'boolean',
    ];

    public function evolutionChain(): BelongsTo
    {
        return $this->belongsTo(EvolutionChain::class);
    }

    public function species(): BelongsTo
    {
        return $this->belongsTo(PokemonSpecies::class, 'species_id');
    }

    public function evolvesToSpecies(): BelongsTo
    {
        return $this->belongsTo(PokemonSpecies::class, 'evolves_to_species_id');
    }
}