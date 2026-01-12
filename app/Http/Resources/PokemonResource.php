<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PokemonResource extends JsonResource
{
    /**
     * Transform the resource into an array for the teambuilder frontend.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Get stats as an associative array
        $stats = $this->stats->pluck('base_stat', 'stat_name')->toArray();

        return [
            'id' => $this->api_id,
            'name' => $this->name,
            'sprite' => $this->sprite_front_default,
            'types' => $this->types->pluck('name')->toArray(),
            'stats' => [
                'hp' => $stats['hp'] ?? null,
                'attack' => $stats['attack'] ?? null,
                'defense' => $stats['defense'] ?? null,
                'specialAttack' => $stats['special-attack'] ?? null,
                'specialDefense' => $stats['special-defense'] ?? null,
                'speed' => $stats['speed'] ?? null,
            ],
            // Optional: Include additional data
            'height' => $this->height,
            'weight' => $this->weight,
            'baseExperience' => $this->base_experience,
        ];
    }
}
