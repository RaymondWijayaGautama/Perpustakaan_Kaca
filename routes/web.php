<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KoleksiController;

Route::get('/generate-barcode', [KoleksiController::class, 'index']);
Route::post('/generate-barcode', [KoleksiController::class, 'generate']);

Route::get('/', function () {
    return view('welcome');
});