<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;




Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});

Route::middleware('auth:sanctum')->get('/test-auth', function () {
    if (Auth::check()) {
        Log::info('User is authenticated:', ['user_id' => Auth::id()]);

        return response()->json(['user_id' => Auth::id()]);
    } else {
        Log::error('User not authenticated. Session data:', session()->all());
        return response()->json(['error' => 'Unauthenticated'], 401);
    }
});