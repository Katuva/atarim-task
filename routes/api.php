<?php

use App\Http\Controllers\UrlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/encode', [UrlController::class, 'encode']);
Route::get('/decode/{code}', [UrlController::class, 'decode']);
