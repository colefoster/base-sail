<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'api_id',
        'name',
        'cost',
        'fling_power',
        'fling_effect',
        'category',
        'effect',
        'short_effect',
        'flavor_text',
        'sprite',
    ];

    protected $casts = [
        'api_id' => 'integer',
        'cost' => 'integer',
        'fling_power' => 'integer',
    ];

    public function pokemon(): BelongsToMany
    {
        return $this->belongsToMany(Pokemon::class, 'pokemon_item')
            ->withPivot('rarity', 'version')
            ->withTimestamps();
    }
}