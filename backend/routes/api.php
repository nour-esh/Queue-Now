<?php

use App\Http\Controllers\Api\v1\ServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::get('/services', [ServiceController::class, 'index']);
    Route::post('/services', [ServiceController::class, 'store']);
    Route::get('/services/{service}', [ServiceController::class, 'show']);
    Route::patch('/services/{service}', [ServiceController::class, 'update']);

});