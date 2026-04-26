<?php

use Illuminate\Support\Facades\Route;

Route::get('/hello', function () {
    return 'Hello, World!';
});

Route::prefix('v1')->group(function () {
    Route::apiResource('books', 'App\Http\Controllers\booksController')->only(['index', 'store', 'show', 'update', 'destroy']);
});
