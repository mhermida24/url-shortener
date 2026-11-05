<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\RedirectController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('links', LinkController::class)->except('store');

    Route::post('links', [LinkController::class, 'store'])
        ->middleware('throttle:create-links')
        ->name('links.store');
});

require __DIR__.'/auth.php';

Route::get('/{code}', RedirectController::class)
    ->where('code', '[A-Za-z0-9_-]+')
    ->middleware('throttle:redirect')
    ->name('links.redirect');
