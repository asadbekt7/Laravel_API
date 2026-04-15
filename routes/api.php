<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ModelsController;
use App\Http\Controllers\ComputerModelController;
use App\Http\Controllers\NetworkController;
use App\Http\Controllers\PrinterModelController;
use App\Http\Controllers\StatusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('categories', [CategoriesController::class, 'index']);
Route::get('categories/{id}', [CategoriesController::class, 'show']);
Route::apiResource('models', ModelsController::class);
Route::get('status', [StatusController::class, 'index']);
Route::get('status/{id}', [StatusController::class, 'show']);
Route::get('network', [NetworkController::class, 'index']);
Route::get('network/{id}', [NetworkController::class, 'show']);
