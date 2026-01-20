<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PokemonNameService;
use App\Services\SpriteUrlService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SpriteController extends Controller
{
    public function __construct(
        private SpriteUrlService $spriteService,
        private PokemonNameService $nameService
    ) {}

    /**
     * Get Pokemon sprite URL by ID.
     */
    public function pokemon(Request $request, int $id): JsonResponse|RedirectResponse
    {
        return $this->buildPokemonResponse($request, $id);
    }

    /**
     * Get Pokemon sprite URL by name.
     */
    public function pokemonByName(Request $request, string $name): JsonResponse|RedirectResponse
    {
        $resolution = $this->nameService->resolve($name);

        if (! $resolution['found']) {
            return response()->json([
                'error' => 'Pokemon not found',
                'name' => $name,
                'normalized' => $resolution['normalized'],
                'suggestions' => $resolution['suggestions'] ?? [],
            ], 404);
        }

        return $this->buildPokemonResponse(
            $request,
            $resolution['id'],
            $resolution['name'],
            $resolution['showdown_name']
        );
    }

    /**
     * Build the Pokemon sprite response.
     */
    private function buildPokemonResponse(
        Request $request,
        int $id,
        ?string $name = null,
        ?string $showdownName = null
    ): JsonResponse|RedirectResponse {
        $variant = $request->query('variant', 'front');
        $shiny = filter_var($request->query('shiny', false), FILTER_VALIDATE_BOOLEAN);
        $female = filter_var($request->query('female', false), FILTER_VALIDATE_BOOLEAN);
        $style = $request->query('style', 'default');
        $generation = $request->query('generation');
        $game = $request->query('game');
        $redirect = filter_var($request->query('redirect', false), FILTER_VALIDATE_BOOLEAN);

        $url = $this->spriteService->pokemon($id, $variant, $shiny, $female, $style, $generation, $game, $showdownName);

        if ($redirect) {
            return redirect()->away($url);
        }

        $response = [
            'id' => $id,
            'url' => $url,
            'options' => [
                'variant' => $variant,
                'shiny' => $shiny,
                'female' => $female,
                'style' => $style,
                'generation' => $generation,
                'game' => $game,
            ],
        ];

        if ($name) {
            $response['name'] = $name;
        }
        if ($showdownName) {
            $response['showdown_name'] = $showdownName;
        }

        return response()->json($response);
    }

    /**
     * Get item sprite URL by name.
     */
    public function item(Request $request, string $name): JsonResponse|RedirectResponse
    {
        $redirect = filter_var($request->query('redirect', false), FILTER_VALIDATE_BOOLEAN);
        $url = $this->spriteService->item($name);

        if ($redirect) {
            return redirect()->away($url);
        }

        return response()->json([
            'name' => strtolower($name),
            'url' => $url,
        ]);
    }

    /**
     * Get type sprite URL.
     */
    public function type(Request $request, string $name): JsonResponse|RedirectResponse
    {
        $generation = $request->query('generation', 'ix');
        $redirect = filter_var($request->query('redirect', false), FILTER_VALIDATE_BOOLEAN);
        $url = $this->spriteService->type($name, $generation);

        if ($redirect) {
            return redirect()->away($url);
        }

        return response()->json([
            'name' => strtolower($name),
            'generation' => $generation,
            'url' => $url,
        ]);
    }

    /**
     * Get badge sprite URL.
     */
    public function badge(Request $request, string $name): JsonResponse|RedirectResponse
    {
        $redirect = filter_var($request->query('redirect', false), FILTER_VALIDATE_BOOLEAN);
        $url = $this->spriteService->badge($name);

        if ($redirect) {
            return redirect()->away($url);
        }

        return response()->json([
            'name' => strtolower($name),
            'url' => $url,
        ]);
    }

    /**
     * List available Pokemon sprite styles.
     */
    public function pokemonStyles(): JsonResponse
    {
        return response()->json($this->spriteService->getStyles());
    }

    /**
     * List available generations for version-specific sprites.
     */
    public function pokemonGenerations(): JsonResponse
    {
        return response()->json($this->spriteService->getGenerations());
    }

    /**
     * Get multiple Pokemon sprites at once.
     */
    public function pokemonBatch(Request $request): JsonResponse
    {
        $ids = $request->query('ids', '');
        $variant = $request->query('variant', 'front');
        $shiny = filter_var($request->query('shiny', false), FILTER_VALIDATE_BOOLEAN);
        $female = filter_var($request->query('female', false), FILTER_VALIDATE_BOOLEAN);
        $style = $request->query('style', 'default');

        $idList = array_filter(array_map('intval', explode(',', $ids)));

        $sprites = [];
        foreach ($idList as $id) {
            $sprites[$id] = $this->spriteService->pokemon($id, $variant, $shiny, $female, $style);
        }

        return response()->json([
            'sprites' => $sprites,
            'options' => [
                'variant' => $variant,
                'shiny' => $shiny,
                'female' => $female,
                'style' => $style,
            ],
        ]);
    }
}
