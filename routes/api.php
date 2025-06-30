<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CommentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Blog API Routes
Route::prefix('blog')->group(function () {
    
    // Posts
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/featured', [PostController::class, 'featured']);
    Route::get('/posts/recent', [PostController::class, 'recent']);
    Route::get('/posts/{slug}', [PostController::class, 'show']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
    
    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);
    Route::get('/categories/{slug}/posts', [CategoryController::class, 'posts']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    
    // Tags
    Route::get('/tags', [TagController::class, 'index']);
    Route::get('/tags/popular', [TagController::class, 'popular']);
    Route::get('/tags/{slug}', [TagController::class, 'show']);
    Route::get('/tags/{slug}/posts', [TagController::class, 'posts']);
    Route::post('/tags', [TagController::class, 'store']);
    Route::put('/tags/{tag}', [TagController::class, 'update']);
    Route::delete('/tags/{tag}', [TagController::class, 'destroy']);
    
    // Comments
    Route::get('/posts/{postSlug}/comments', [CommentController::class, 'index']);
    Route::post('/comments', [CommentController::class, 'store']);
    Route::get('/comments/{comment}', [CommentController::class, 'show']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    Route::get('/comments/pending', [CommentController::class, 'pending']);
    Route::get('/comments/recent', [CommentController::class, 'recent']);
    Route::patch('/comments/{comment}/approve', [CommentController::class, 'approve']);
    Route::patch('/comments/{comment}/reject', [CommentController::class, 'reject']);
});
