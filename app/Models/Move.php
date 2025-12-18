<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Move extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'api_id',
        'name',
        'power',
        'pp',
        'accuracy',
        'priority',
        'type_id',
        'damage_class',
        'effect_chance',
        'contest_type',
        'generation',
        'effect',
        'short_effect',
        'flavor_text',
        'target',
        'ailment',
        'meta_category',
        'min_hits',
        'max_hits',
        'min_turns',
        'max_turns',
        'drain',
        'healing',
        'crit_rate',
        'ailment_chance',
        'flinch_chance',
        'stat_chance',
    ];

    protected $casts = [
        'api_id' => 'integer',
        'power' => 'integer',
        'pp' => 'integer',
        'accuracy' => 'integer',
        'priority' => 'integer',
        'type_id' => 'integer',
        'effect_chance' => 'integer',
        'min_hits' => 'integer',
        'max_hits' => 'integer',
        'min_turns' => 'integer',
        'max_turns' => 'integer',
        'drain' => 'integer',
        'healing' => 'integer',
        'crit_rate' => 'integer',
        'ailment_chance' => 'integer',
        'flinch_chance' => 'integer',
        'stat_chance' => 'integer',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', function ($query) {
            $query->orderBy('api_id');
        });
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class);
    }

    public function pokemon(): BelongsToMany
    {
        return $this->belongsToMany(Pokemon::class, 'move_pokemon')
            ->withPivot('learn_method', 'level_learned_at')
            ->withTimestamps();
    }
}
