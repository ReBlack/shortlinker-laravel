<?php

use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UrlController::class, 'index']);
Route::get('/{shortHash}', [UrlController::class, 'redirect'])->where('shortHash', '[a-zA-Z0-9]{8}');
