<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Guru\TesController;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Guru\SoalController;
use App\Http\Controllers\API\Guru\PenilaianEssayController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
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

        // Essay assessment routes
        Route::prefix('/penilaian')->group(function() {
            Route::get('/tests', [PenilaianEssayController::class, 'getTests']);
            Route::get('/test/{tes_id}/essays', [PenilaianEssayController::class, 'getEssayAnswers']);
            Route::post('/essay/{jawaban_id}', [PenilaianEssayController::class, 'gradeEssay']);
            Route::post('/essays/batch', [PenilaianEssayController::class, 'batchGradeEssays']);
        });
    });
});
