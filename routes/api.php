<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\MeetingRoomController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('bookings', BookingController::class);
    Route::get('/calendar', [BookingController::class, 'index']);
    Route::apiResource('/rooms', MeetingRoomController::class);
});

