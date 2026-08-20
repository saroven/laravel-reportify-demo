<?php

use App\Http\Controllers\DownloadController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('users.index');
});

Route::get('/users', [UserController::class, 'index'])->name('users.index');

Route::get('/downloads', [DownloadController::class, 'index'])->name('downloads.index');
Route::get('/downloads/{download}/file', [DownloadController::class, 'download'])->name('downloads.download');
Route::delete('/downloads/{download}', [DownloadController::class, 'destroy'])->name('downloads.destroy');

