<?php

use App\Http\Controllers\Api\PokemonController;
use App\Http\Controllers\Api\SpriteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Pokemon API routes (database)
Route::prefix('pokemon')->group(function () {
    Route::get('/', [PokemonController::class, 'index']);
    Route::get('/search', [PokemonController::class, 'search']);
    Route::get('/{apiId}', [PokemonController::class, 'show'])->where('apiId', '[0-9]+');
});

// Format/Smogon API routes
Route::prefix('formats/{format}')->group(function () {
    // Sets endpoints (Smogon data only)
    Route::get('/sets', [PokemonController::class, 'getSets']);
    Route::get('/sets/search', [PokemonController::class, 'searchSets']);

    // Names endpoints
    Route::get('/names', [PokemonController::class, 'getNames']);
    Route::get('/names/usage', [PokemonController::class, 'getNamesWithUsage']);

    // Pokemon endpoints (database data for Pokemon in format)
    Route::get('/pokemon', [PokemonController::class, 'getPokemonInFormat']);
    Route::get('/pokemon/search', [PokemonController::class, 'searchPokemonInFormat']);

    // Combined endpoints (sets + database data)
    Route::get('/combined', [PokemonController::class, 'getCombined']);
    Route::get('/combined/search', [PokemonController::class, 'searchCombined']);

    // Usage stats endpoints
    Route::get('/stats', [PokemonController::class, 'getStats']);
    Route::get('/stats/search', [PokemonController::class, 'searchStats']);
    Route::get('/stats/ranked', [PokemonController::class, 'getStatsRanked']);
    Route::get('/stats/combined', [PokemonController::class, 'getStatsCombined']);
    Route::get('/stats/{pokemon}', [PokemonController::class, 'getStatsForPokemon']);

    // Pokemon-specific usage data endpoints
    Route::get('/pokemon/{pokemon}/moves', [PokemonController::class, 'getPokemonMovesWithUsage']);
    Route::get('/pokemon/{pokemon}/abilities', [PokemonController::class, 'getPokemonAbilitiesWithUsage']);
    Route::get('/pokemon/{pokemon}/items', [PokemonController::class, 'getPokemonItemsWithUsage']);
    Route::get('/pokemon/{pokemon}/teammates', [PokemonController::class, 'getPokemonTeammates']);
    Route::get('/pokemon/{pokemon}/counters', [PokemonController::class, 'getPokemonCounters']);
    Route::get('/pokemon/{pokemon}/spreads', [PokemonController::class, 'getPokemonSpreads']);
});

// Moves API routes
Route::prefix('moves')->group(function () {
    Route::get('/search', [PokemonController::class, 'searchMoves']);
});

// Items API routes
Route::prefix('items')->group(function () {
    Route::get('/search', [PokemonController::class, 'searchItems']);
});

// Types API routes
Route::prefix('types')->group(function () {
    Route::get('/', [PokemonController::class, 'getTypes']);
});

// Natures API route (static data)
Route::get('/natures', [PokemonController::class, 'getNatures']);

// Sprite API routes (wraps GitHub raw URLs from PokeAPI/sprites)
Route::prefix('sprites')->group(function () {
    // Pokemon sprites
    Route::get('/pokemon/styles', [SpriteController::class, 'pokemonStyles']);
    Route::get('/pokemon/generations', [SpriteController::class, 'pokemonGenerations']);
    Route::get('/pokemon/batch', [SpriteController::class, 'pokemonBatch']);
    Route::get('/pokemon/name/{name}', [SpriteController::class, 'pokemonByName']);
    Route::get('/pokemon/{id}', [SpriteController::class, 'pokemon'])->where('id', '[0-9]+');

    // Item sprites
    Route::get('/items/{name}', [SpriteController::class, 'item']);

    // Type sprites
    Route::get('/types/{name}', [SpriteController::class, 'type']);

    // Badge sprites
    Route::get('/badges/{name}', [SpriteController::class, 'badge']);
});
