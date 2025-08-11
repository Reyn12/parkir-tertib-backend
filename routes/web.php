<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/', function () {
    return view('welcome');
});

// Admin routes untuk approve posts
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/posts', [PostController::class, 'adminIndex'])->name('posts.index');
    Route::post('/posts/{id}/approve', [PostController::class, 'approve'])->name('posts.approve');
    Route::post('/posts/{id}/reject', [PostController::class, 'reject'])->name('posts.reject');
});
