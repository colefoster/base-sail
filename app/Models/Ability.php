<?php

namespace App\Models;

use App\Models\Traits\FormatsNameAttribute;
use App\Models\Traits\HasApiRouteKey;
use App\Models\Traits\OrderedByApiId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ability extends Model
{
    use FormatsNameAttribute, HasApiRouteKey, HasFactory, OrderedByApiId, SoftDeletes;

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

    public function pokemon(): BelongsToMany
    {
        return $this->belongsToMany(Pokemon::class, 'ability_pokemon')
            ->withPivot('is_hidden', 'slot')
            ->withTimestamps();
    }
}
