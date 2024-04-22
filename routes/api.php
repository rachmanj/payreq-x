<?php

use App\Http\Controllers\Api\MigrasiController;
use App\Http\Controllers\BucSyncController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('rabs/get-payreqs', [BucSyncController::class, 'get_buc_payreqs'])->name('get_buc_payreqs');

Route::prefix('migrasi')->name('migrasi.')->group(function () {
    Route::get('/', [MigrasiController::class, 'index'])->name('index');
    Route::get('/departments', [MigrasiController::class, 'departments'])->name('departments');
});
