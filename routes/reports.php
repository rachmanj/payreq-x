<?php

use App\Http\Controllers\Reports\EquipmentController;
use App\Http\Controllers\Reports\LoanController;
use App\Http\Controllers\Reports\OngoingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reports\ReportIndexController;

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportIndexController::class, 'index'])->name('index');

    Route::prefix('ongoing')->name('ongoing.')->group(function () {
        Route::get('/', [OngoingController::class, 'index'])->name('index');
        Route::get('/data', [OngoingController::class, 'data'])->name('data');
        Route::get('/{int}/project', [OngoingController::class, 'project_index'])->name('project');
    });

    Route::prefix('equipment')->name('equipment.')->group(function () {
        Route::get('/', [EquipmentController::class, 'index'])->name('index');
        Route::get('/data', [EquipmentController::class, 'data'])->name('data');
        // add route with query of unit_no
        Route::get('/unit_no', [EquipmentController::class, 'detail'])->name('detail');
    });

    Route::prefix('loan')->name('loan.')->group(function () {
        Route::get('/', [LoanController::class, 'index'])->name('index');
        Route::get('/index-7997', [LoanController::class, 'index_7997'])->name('index_7997');
        Route::get('/data', [LoanController::class, 'data'])->name('data');
        Route::get('/paid-data', [LoanController::class, 'paid_data'])->name('paid_data');
        Route::post('/update', [LoanController::class, 'update'])->name('update');
    });
});
