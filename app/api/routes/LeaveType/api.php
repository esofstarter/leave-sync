<?php

use Illuminate\Http\Request;
use App\Applications\LeaveType\Controllers\LeaveTypeController;
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
        'prefix' => 'leave_type',
    ], function () {
        Route::get('all', [LeaveTypeController::class, 'getAll']);
        Route::get('draw', [LeaveTypeController::class, 'draw']);

        // CRUD ROUTES
        Route::post('create', [LeaveTypeController::class, 'create']);
        Route::get('{id}', [LeaveTypeController::class, 'get']);
        Route::patch('{id}', [LeaveTypeController::class, 'update']);
        Route::delete('{id}', [LeaveTypeController::class, 'delete']);
        Route::post('{id}/delete', [LeaveTypeController::class, 'delete']);
    });
});
