<?php

use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\MoveController;
use App\Http\Controllers\Api\PokemonController;
use App\Http\Controllers\Api\PokemonUsageController;
use App\Http\Controllers\Api\SmogonFormatController;
use App\Http\Controllers\Api\SmogonStatsController;
use App\Http\Controllers\Api\SpriteController;
use App\Http\Controllers\Api\TypeController;
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
    Route::get('/sets', [SmogonFormatController::class, 'getSets']);
    Route::get('/sets/search', [SmogonFormatController::class, 'searchSets']);

    // Names endpoints
    Route::get('/names', [SmogonFormatController::class, 'getNames']);
    Route::get('/names/usage', [SmogonFormatController::class, 'getNamesWithUsage']);

    // Pokemon endpoints (database data for Pokemon in format)
    Route::get('/pokemon', [SmogonFormatController::class, 'getPokemonInFormat']);
    Route::get('/pokemon/search', [SmogonFormatController::class, 'searchPokemonInFormat']);

    // Combined endpoints (sets + database data)
    Route::get('/combined', [SmogonFormatController::class, 'getCombined']);
    Route::get('/combined/search', [SmogonFormatController::class, 'searchCombined']);

    // Usage stats endpoints
    Route::get('/stats', [SmogonStatsController::class, 'getStats']);
    Route::get('/stats/search', [SmogonStatsController::class, 'searchStats']);
    Route::get('/stats/ranked', [SmogonStatsController::class, 'getStatsRanked']);
    Route::get('/stats/combined', [SmogonStatsController::class, 'getStatsCombined']);
    Route::get('/stats/{pokemon}', [SmogonStatsController::class, 'getStatsForPokemon']);

    // Pokemon-specific usage data endpoints
    Route::get('/pokemon/{pokemon}/moves', [PokemonUsageController::class, 'getMoves']);
    Route::get('/pokemon/{pokemon}/abilities', [PokemonUsageController::class, 'getAbilities']);
    Route::get('/pokemon/{pokemon}/items', [PokemonUsageController::class, 'getItems']);
    Route::get('/pokemon/{pokemon}/teammates', [PokemonUsageController::class, 'getTeammates']);
    Route::get('/pokemon/{pokemon}/counters', [PokemonUsageController::class, 'getCounters']);
    Route::get('/pokemon/{pokemon}/spreads', [PokemonUsageController::class, 'getSpreads']);
});

// Moves API routes
Route::prefix('moves')->group(function () {
    Route::get('/search', [MoveController::class, 'search']);
});

// Items API routes
Route::prefix('items')->group(function () {
    Route::get('/search', [ItemController::class, 'search']);
});

// Types API routes
Route::prefix('types')->group(function () {
    Route::get('/', [TypeController::class, 'index']);
});

// Natures API route (static data)
Route::get('/natures', [TypeController::class, 'natures']);

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
