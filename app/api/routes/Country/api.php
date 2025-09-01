<?php

use Illuminate\Http\Request;
use App\Applications\Country\Controllers\CountryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| This file contains the API routes for the User module
|
|
*/

// AUTHORIZED ROUTES
Route::group([
    'middleware' => 'auth:sanctum'
], function () {
    Route::group([
        'prefix' => 'country',
    ], function () {
        Route::get('all', [CountryController::class, 'getAll']);
        Route::get('draw', [CountryController::class, 'draw']);

        // CRUD ROUTES
        Route::post('create', [CountryController::class, 'create']);
        Route::get('{id}', [CountryController::class, 'get']);
        Route::patch('{id}', [CountryController::class, 'update']);
        Route::delete('{id}', [CountryController::class, 'delete']);
        Route::post('{id}/delete', [CountryController::class, 'delete']);
    });
});
