<?php

use App\Http\Controllers\ShortUrlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// for api url shortener
// Route::get('/short-url/{url}', [ShortUrlController::class, 'getShortUrl'])
//     ->where('url', '^(http|https)://[a-zA-Z0-9.-]+(:[0-9]+)?(/.*)?$')
//     ->middleware('web');
Route::get('/short-url', [ShortUrlController::class, 'getShortUrl'])->middleware('web');