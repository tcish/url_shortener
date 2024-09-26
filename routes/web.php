<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShortUrlController;
use Illuminate\Support\Facades\Route;

// ! short url routes for un-auth user
Route::get('/', [ShortUrlController::class, 'index'])->name('short-url.index');
Route::resource('short-url', ShortUrlController::class)->except(['index', 'update']);
Route::get('/go/{short_code}', [ShortUrlController::class, 'redirectUrl']);

Route::middleware('auth')->group(function () {
    // ! default routes by breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ! short routes url for auth user
    Route::post('/short-url/{id}', [ShortUrlController::class, 'update']);
    Route::get('/insight/{id}', [ShortUrlController::class, 'urlInsights'])->name('short-url.insights');
});

require __DIR__.'/auth.php';
