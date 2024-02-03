<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    // user 
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user', [AuthController::class, 'update']);

    // post 
    Route::get('/posts', [PostController::class, 'index']); // all posts
    Route::post('/posts', [PostController::class, 'store']); // create post
    Route::get('/posts/{slug}', [PostController::class, 'show']); // single post
    Route::put('/posts/{slug}', [PostController::class, 'update']); // update single post
    Route::delete('/posts/{slug}', [PostController::class, 'destroy']); // delete post

    // comment 
    Route::get('/posts/{slug}/comments', [CommentController::class, 'index']); // all comments
    Route::post('/posts/{slug}/comments', [CommentController::class, 'store']); // create comment
    Route::put('/comments/{id}', [CommentController::class, 'update']); // update single comment
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']); // delete comment

    // like
    Route::post('/posts/{slug}/likes', [LikeController::class, 'likeOrUnlike']); // like or dislike post
});
