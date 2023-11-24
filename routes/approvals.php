<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApprovalPlanController;
use App\Http\Controllers\ApprovalRequestPayreqController;
use App\Http\Controllers\ApprovalRequestRabController;
use App\Http\Controllers\ApprovalRequestRealizationController;


Route::prefix('approvals')->name('approvals.')->group(function () {
    Route::prefix('request')->name('request.')->group(function () {
        Route::prefix('payreqs')->name('payreqs.')->group(function () {
            Route::get('/data', [ApprovalRequestPayreqController::class, 'data'])->name('data'); // 'approvals.request.payreqs.data'
            Route::get('/', [ApprovalRequestPayreqController::class, 'index'])->name('index'); // 'approvals.request.payreqs.index'
            Route::get('/{id}', [ApprovalRequestPayreqController::class, 'show'])->name('show'); // 'approvals.request.payreqs.show'
        });
        Route::prefix('realizations')->name('realizations.')->group(function () {
            Route::get('/data', [ApprovalRequestRealizationController::class, 'data'])->name('data');
            Route::get('/', [ApprovalRequestRealizationController::class, 'index'])->name('index');
            Route::get('/{id}', [ApprovalRequestRealizationController::class, 'show'])->name('show');
        });
        Route::prefix('rabs')->name('rabs.')->group(function () {
            Route::get('/data', [ApprovalRequestRabController::class, 'data'])->name('data');
            Route::get('/', [ApprovalRequestRabController::class, 'index'])->name('index');
        });
    });
    Route::prefix('plan')->name('plan.')->group(function () {
        Route::put('/{id}/update', [ApprovalPlanController::class, 'update'])->name('update');
    });
});
