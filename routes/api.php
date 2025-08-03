<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('check.token')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public routes (tidak perlu login)
Route::get('/posts', [PostController::class, 'index']);

// Protected routes
Route::middleware('check.token')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::get('/users/{id}', [AuthController::class, 'showUser']);
    
    // Post routes (perlu login)
    Route::get('/posts/{id}', [PostController::class, 'show']);
    Route::get('/posts/user/{user_id}', [PostController::class, 'getByUser']);
    Route::get('/posts/category/{category_id}', [PostController::class, 'getByCategory']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    
    // Comment routes (perlu login)
    Route::post('/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
    Route::get('/posts/{post_id}/comments', [CommentController::class, 'getByPost']);
    
    // Like routes (perlu login)
    Route::post('/likes/toggle', [LikeController::class, 'toggle']);
    Route::get('/posts/{post_id}/like-status', [LikeController::class, 'checkLike']);
    Route::get('/posts/{post_id}/likes', [LikeController::class, 'getLikesByPost']);
}); 