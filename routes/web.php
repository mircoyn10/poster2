<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\BuyCoinsController;
use App\Models\CoinPackage;
use App\Http\Controllers\CoinPackageControllerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\GoogleTrendsController;
use App\Http\Controllers\SearchHistoryController;
use App\Http\Controllers\ClearHistoryController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\PosterStoryController;

Route::get('/community', [CommunityController::class, 'index'])->name('community.index');
Route::get('/poster/story', [PosterStoryController::class, 'index'])->name('PosterStory.index');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
// Rotte per il blog
Route::middleware(['web'])->group(function () {
    Route::get('/dashboard/articles', function () {
        return Inertia::render('DashboardArticles');
    })->name('dashboard.articles');


});

// routes/web.php
Route::get('/articles/latest', [ArticleController::class, 'latest']);

Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/article/{id}', [ArticleController::class, 'show']);
Route::post('/articles', [ArticleController::class, 'store']);

// Rotte per la knowledge base
Route::get('/blog', function () {
    return Inertia::render('Blog');
});
Route::get('/KnowledgeBase', function () {
    return Inertia::render('KnowledgeBase');
});
Route::get('/user-prompts', [SearchHistoryController::class, 'getUserPrompts'])->middleware('auth');

// Gestisce la rotta /trends senza parametro

use App\Http\Controllers\EventController;
Route::get('/seo', function () {
    return Inertia::render('Seo');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/calendar', function () {
        return inertia('Calendar'); // Assumendo che tu stia usando InertiaJS
    });
});


Route::get('/trends', function () {
    return Inertia::render('Trends'); // Nome del file Vue senza estensione
})->name('trends.index');


Route::get('/trends/{country}', [GoogleTrendsController::class, 'fetchTrends'])->name('trends.fetch');






Route::get('/search-history', [SearchHistoryController::class, 'getSearchHistory']);
Route::post('/clear-history', [ClearHistoryController::class, 'clearHistory']);
// Middleware 'auth' verifica che l'utente sia autenticato via sessione


// Route per gestire il ritorno da PayPal dopo l'approvazione
Route::get('/payment/success', [PaymentController::class, 'executePayment'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'executePayment'])->name('payment.cancel');
Route::post('/payment/create', [PaymentController::class, 'createPayment'])->Middleware('web');
Route::post('/payment/execute', [PaymentController::class, 'executePayment'])->Middleware('web');

Route::get('/buy-coins', [BuyCoinsController::class, 'index'])->name('buy-coins');


Route::middleware('web')->get('/user/coin', function (Request $request) {
    return response()->json([
        'coin' => $request->user()->coin, // Restituisce il numero di coin dell'utente autenticato
    ]);
});

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