<?php

namespace App\Models;

use App\Models\Traits\FormatsNameAttribute;
use App\Models\Traits\HasApiRouteKey;
use App\Models\Traits\OrderedByApiId;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use FormatsNameAttribute, HasApiRouteKey, HasFactory, OrderedByApiId, SoftDeletes;

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

    protected function category(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucwords(str_replace('-', ' ', $value)),
        );
    }

    public function pokemon(): BelongsToMany
    {
        return $this->belongsToMany(Pokemon::class, 'pokemon_item')
            ->withPivot('rarity', 'version')
            ->withTimestamps();
    }
}
