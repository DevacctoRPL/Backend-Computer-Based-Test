<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Guru\TesController;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Guru\SoalController;
use App\Http\Controllers\API\SesiTes\SesiTesController;
use App\Http\Controllers\API\MonitoringAktivitas\MonitoringAktivitasController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::prefix('/guru')->group(function() {
        Route::prefix('/tes')->group(function() {
            Route::post('/create', [TesController::class,'createTes']);
            Route::put('/update/{tes_id}', [TesController::class,'updateTes']);
            Route::get('/show', [TesController::class, 'showTes']);
            Route::get('/show/{tes_id}', [TesController::class, 'showTesById']);
            Route::put('/setstatus/{tes_id}', [TesController::class, 'setStatus']);
            Route::delete('/delete/{tes_id}', [TesController::class, 'deleteTes']);
            Route::delete('/deleteall', [TesController::class, 'deleteAllTes']);
        });

        Route::prefix('/soal')->group(function() {
            Route::post('/create/{tes_id}', [SoalController::class,'createSoal']);
            Route::get('/show/{tes_id}', [SoalController::class, 'showSoal']);
            Route::delete('/deleteall/{tes_id}', [SoalController::class, 'deleteAllSoal']);
        });

        

    });
    

    

});
Route::prefix('/monitoring-aktivitas')->group(function() {
    Route::get('/show', [MonitoringAktivitasController::class, 'index']);
    Route::get('/{id}', [MonitoringAktivitasController::class, 'show']);
    Route::post('/insert', [MonitoringAktivitasController::class, 'store']);
    Route::put('/{id}', [MonitoringAktivitasController::class, 'update']);
    Route::delete('/{id}', [MonitoringAktivitasController::class, 'destroy']);
});

Route::prefix('/sesi-tes')->group(function() {
    Route::get('/', [SesiTesController::class, 'index']);
    Route::get('/{id}', [SesiTesController::class, 'show']);
    Route::post('/', [SesiTesController::class, 'store']);
    Route::put('/{id}', [SesiTesController::class, 'update']);
    Route::delete('/{id}', [SesiTesController::class, 'destroy']);
});