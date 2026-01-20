<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/teambuilder', function () {
    return Inertia::render('Teambuilder');
})->name('teambuilder');

Route::get('/pokebuilder', function () {
    return Inertia::render('Pokebuilder');
})->name('pokebuilder');

Route::get('/home', function () {
    return Inertia::render('Home');
})->name('home');
