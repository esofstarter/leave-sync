<?php

use Illuminate\Http\Request;
use App\Applications\NationalHoliday\Controllers\NationalHolidayController;
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
        'prefix' => 'national_holiday',
    ], function () {
        Route::get('all', [NationalHolidayController::class, 'getAll']);
        Route::get('draw', [NationalHolidayController::class, 'draw']);

        // CRUD ROUTES
        Route::post('create', [NationalHolidayController::class, 'create']);
        Route::get('{id}', [NationalHolidayController::class, 'get']);
        Route::patch('{id}', [NationalHolidayController::class, 'update']);
        Route::post('{id}/delete', [NationalHolidayController::class, 'delete']);
    });
});
