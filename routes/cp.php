<?php

use Huement\StatamicYtListr\Http\Controllers\Cp\SyncController;
use Illuminate\Support\Facades\Route;

Route::name('ytlistr.')
  ->prefix('ytlistr')
  ->group(function () {
    Route::get('/', [SyncController::class, 'index'])->name('index');
    Route::post('/sync', [SyncController::class, 'sync'])->name('sync');
    Route::delete('/{video}', [SyncController::class, 'destroy'])->name(
      'destroy'
    );
  });
