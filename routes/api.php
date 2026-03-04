<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\HomeController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\ProfileController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::middleware(['json'])->prefix('auth')->group(function () {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('verfy', [AuthController::class, 'userVerfy']);

    Route::get('/clear-all-cache', function () {
        Artisan::call('optimize:clear');
        return "All caches cleared! You can now delete this route.";
    });

    Route::get('states', [AuthController::class, 'states']);
    Route::get('district/{id}', [AuthController::class, 'districts']);
});

Route::middleware((['json', 'auth.api:USER']))->prefix('secure')->group(function () {
    
Route::post('save-fcm-token', [AuthController::class, 'saveFcmToken']);
    Route::prefix('profile')->group(function () {
        Route::get('', [ProfileController::class, 'getProfile']);
        Route::post('/update', [ProfileController::class, 'UpdateProfile']);
    });

    Route::prefix('home')->group(function () {
        Route::get('/category', [HomeController::class, 'categories']);
        Route::get('/fresh-recommendations', [HomeController::class, 'freshRecommendations']);
        Route::get('/category/adds/{id}', [HomeController::class, 'categoryProducts']);
    });

    Route::prefix('adds')->group(function () {
        Route::get('', [ProductController::class, 'myProducts']);
        Route::get('product/{id}', [ProductController::class, 'productDetail']);
        Route::get('favorites', [ProductController::class, 'myFavoriteProducts']);
        Route::post('add', [ProductController::class, 'addPost']);
        Route::post('subscription/{id}', [ProductController::class, 'makeProductBoost']);
        Route::get('subscriptions', [ProductController::class, 'subscriptions']);
        Route::get('delete/{id}', [ProductController::class, 'deletePostImages']);
        Route::post('images/{id}', [ProductController::class, 'addImages']);
        Route::post('edit/{id}', [ProductController::class, 'editPost']);
        Route::get('favorite/add/{id}', [ProductController::class, 'makeFavorite']);
        Route::get('favorite/remove/{id}', [ProductController::class, 'removeFavourite']);
    });
    Route::get('chat/{id}', [ProductController::class, 'productChat']);
    Route::get('messages/{id}', [ProductController::class, 'messages']);
    Route::post('send/messages/{id}', [ProductController::class, 'sendMessage']);
    Route::get('mychats', [ProductController::class, 'myChats']);

    Route::get('requestedusers/{id}', [ProductController::class, 'productrequestUsers']);
    Route::post('sold/{id}', [ProductController::class, 'productSold']);
});
