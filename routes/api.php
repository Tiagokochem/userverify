<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserProcessController;

Route::prefix('v1')->group(function () {
    Route::post('/users/process', [UserProcessController::class, 'process']);
    
    Route::get('/mock/cpf-status/{cpf}', function (string $cpf) {
        $statuses = ['limpo', 'pendente', 'negativado'];
        $status = $statuses[hexdec(substr(sha1($cpf), 0, 2)) % count($statuses)];
        return response()->json(['status' => $status]);
    })->name('mock.cpf.status');
});