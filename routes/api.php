<?php

use App\Http\Controllers\Api\PokemonController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Pokemon API endpoints for teambuilder
Route::prefix('pokemon')->group(function () {
    Route::get('/', [PokemonController::class, 'index'])->name('api.pokemon.index');
    Route::get('/types', [PokemonController::class, 'types'])->name('api.pokemon.types');
    Route::get('/{pokemon}', [PokemonController::class, 'show'])->name('api.pokemon.show');
});
