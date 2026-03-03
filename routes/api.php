<?php

use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

Route::post('/shorten', [UrlController::class, 'shorten']);
Route::get('/url/qr/{shortHash}', [UrlController::class, 'qr'])->where('shortHash', '[a-zA-Z0-9]{8}');
