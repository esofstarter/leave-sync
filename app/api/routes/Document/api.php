<?php

use Illuminate\Http\Request;
use App\Applications\Document\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| This file contains the API routes for the Document module
|
|
*/

// AUTHORIZED ROUTES
Route::group([
    'middleware' => 'auth:sanctum'
], function () {
    Route::group([
        'prefix' => 'document',
    ], function () {
        Route::get('all', [DocumentController::class, 'getAll']);
        Route::get('draw', [DocumentController::class, 'draw']);
        Route::get('{id}', [DocumentController::class, 'get']);
    });
});
