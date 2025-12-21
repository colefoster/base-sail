<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ability extends Model
{
    use HasFactory, SoftDeletes;

    #Table name is abilities not abilitys
    protected $table = 'abilities';

    protected $fillable = [
        'api_id',
        'name',
        'effect',
        'short_effect',
        'is_main_series',
    ];

    protected $casts = [
        'api_id' => 'integer',
        'is_main_series' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', function ($query) {
            $query->orderBy('api_id');
        });
    }

    public function getRouteKeyName(): string
    {
        return 'api_id';
    }

    public function pokemon(): BelongsToMany
    {
        return $this->belongsToMany(Pokemon::class, 'ability_pokemon')
            ->withPivot('is_hidden', 'slot')
            ->withTimestamps();
    }
}
