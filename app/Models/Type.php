<?php

namespace App\Models;

use App\Models\Traits\FormatsNameAttribute;
use App\Models\Traits\HasApiRouteKey;
use App\Models\Traits\OrderedByApiId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Type extends Model
{
    use FormatsNameAttribute, HasApiRouteKey, HasFactory, OrderedByApiId;

    protected $fillable = [
        'api_id',
        'name',
    ];

    protected $casts = [
        'api_id' => 'integer',
    ];

    public function pokemon(): BelongsToMany
    {
        return $this->belongsToMany(Pokemon::class, 'pokemon_type')
            ->withPivot('slot')
            ->withTimestamps();
    }

    public function moves(): HasMany
    {
        return $this->hasMany(Move::class);
    }
}
