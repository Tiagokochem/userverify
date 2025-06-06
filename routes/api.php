<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserProcessController;

Route::prefix('v1')->group(function () {
    Route::post('/users/process', [UserProcessController::class, 'process']);
});
