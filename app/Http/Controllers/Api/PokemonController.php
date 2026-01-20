<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pokemon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PokemonController extends Controller
{
    /**
     * Get paginated list of Pokemon with their types and stats.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page');
        $search = $request->input('search');

        $query = Pokemon::with(['types', 'stats', 'species'])
            ->where('is_default', true);

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if (! $perPage) {
            return response()->json($query->get());
        }

        return response()->json($query->paginate($perPage));
    }

    /**
     * Get a single Pokemon with full details.
     */
    public function show(int $apiId): JsonResponse
    {
        $pokemon = Pokemon::with([
            'types',
            'abilities',
            'moves',
            'stats',
            'species.evolutionChain.evolutions',
        ])
            ->where('is_default', true)
            ->where('api_id', $apiId)
            ->firstOrFail();

        return response()->json($pokemon);
    }

    /**
     * Search Pokemon by name.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q');

        if (! $query) {
            return response()->json([]);
        }

        $pokemon = Pokemon::with(['types', 'stats'])
            ->where('is_default', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('api_id', $query);
            })
            ->limit(20)
            ->get();

        return response()->json($pokemon);
    }
}
