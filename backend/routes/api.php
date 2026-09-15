<?php

use App\Http\Controllers\Api\v1\ServiceController;
use App\Http\Controllers\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::get('/services', [ServiceController::class, 'index']);
    Route::post('/services', [ServiceController::class, 'store']);
    Route::get('/services/{service}', [ServiceController::class, 'show']);
    Route::patch('/services/{service}', [ServiceController::class, 'update']);
    Route::get('/services/{serviceId}/waiting-list', [TicketController::class, 'waitingList']);

    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/tickets/{id}', [TicketController::class, 'show']);
    Route::patch('/tickets/{id}/cancel', [TicketController::class, 'cancel']);

});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');