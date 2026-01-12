<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PokemonResource;
use App\Models\Pokemon;
use Illuminate\Http\Request;

class PokemonController extends Controller
{
    /**
     * Get all Pokemon for the teambuilder.
     * Returns Pokemon with their types and stats loaded.
     */
    public function index(Request $request)
    {
        $query = Pokemon::with(['types', 'stats']);

        // Optional: Filter by generation, type, etc.
        if ($request->has('generation')) {
            $query->whereHas('species', function ($q) use ($request) {
                $q->where('generation', $request->generation);
            });
        }

        if ($request->has('type')) {
            $query->whereHas('types', function ($q) use ($request) {
                $q->where('name', $request->type);
            });
        }

        // Optional: Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Only return default forms (excludes mega evolutions, regional forms, etc.)
        // Remove this if you want all forms
        $query->where('is_default', true);

        // Paginate results (50 per page)
        $pokemon = $query->paginate(50);

        return PokemonResource::collection($pokemon);
    }

    /**
     * Get a single Pokemon by api_id.
     */
    public function show(Pokemon $pokemon)
    {
        $pokemon->load(['types', 'stats', 'abilities']);
        return new PokemonResource($pokemon);
    }

    /**
     * Get all available types for filtering.
     */
    public function types()
    {
        return \App\Models\Type::orderBy('name')->pluck('name');
    }
}
