<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/document', [DocumentController::class, 'store']);
Route::get('/reports/{period}', [DocumentController::class, 'getReport']);
