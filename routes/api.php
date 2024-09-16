<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GenerateContentController;





#Route::middleware('auth:sanctum')->post('/generate-content', [GenerateContentController::class, 'generateContent']);
Route::post('/generate-content', [GenerateContentController::class, 'generateContent'])->middleware('web');
Route::get('/user', function (Request $request) {

    return $request->user();

})->middleware('auth:sanctum');
