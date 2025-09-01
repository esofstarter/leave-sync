<?php

use Illuminate\Http\Request;
use App\Applications\LeaveRequest\Controllers\LeaveRequestController;
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
        'prefix' => 'leave_request',
    ], function () {
        Route::get('all', [LeaveRequestController::class, 'getAll']);
        Route::get('draw', [LeaveRequestController::class, 'draw']);
        Route::get('approved', [LeaveRequestController::class, 'getApproved']);
        Route::get('pending', [LeaveRequestController::class, 'getPending']);
        Route::get('{id}/approved_user', [LeaveRequestController::class, 'getApprovedByUser']);

        // CRUD ROUTES
        Route::post('{id}/approve_update', [LeaveRequestController::class, 'approveUpdate']);
        Route::get('{id}/download', [LeaveRequestController::class, 'downloadLeaveRequestPDF']);
        Route::post('create', [LeaveRequestController::class, 'create']);
        Route::post('{id}/approve', [LeaveRequestController::class, 'approve']);
        Route::post('{id}/decline', [LeaveRequestController::class, 'decline']);
        Route::get('{id}', [LeaveRequestController::class, 'get']);
        Route::patch('{id}', [LeaveRequestController::class, 'update']);
        Route::post('{id}/delete', [LeaveRequestController::class, 'delete']);
    });
});
