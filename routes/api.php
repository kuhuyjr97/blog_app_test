<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;

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
//----- Public Route ------//
    //create route /register use controller
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
// Protected Routes -------//
    Route::middleware('auth:sanctum')->group(function () {
        //create resource route
        Route::resource('blogs', BlogController::class);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::resource('blogs.comments', CommentController::class)->only(['store', 'index', 'update', 'destroy']);


        // Route::get('/blogs/{id}/comments', [CommentController::class, 'index']);
        // Route::post('/blogs/{id}/comments', [CommentController::class, 'store']);
        // Route::put('/blogs/{post_id}/comments/{comment_id}', [CommentController::class, 'update']);
        // Route::delete('/blogs/{post_id}/comments/{cmt_id}', [CommentController::class, 'destroy']);

        // Route::resource('blogs.comments', CommentController::class)->parameters(['comments' => 'id'])
        // ->only(['store', 'index', 'update', 'destroy']);
    });