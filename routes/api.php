<?php

use App\Http\Controllers\SavedPostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AnswerController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\ParagraphController;
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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:sanctum')->group(function () {
    
    Route::middleware('check')->group(function () {

        Route::post('/post', [PostController::class, 'store']);

        Route::put('/post/{id}', [PostController::class, 'update']);

        Route::delete('/post/{id}', [PostController::class, 'destroy']);

        Route::post('/paragraph', [ParagraphController::class, 'store']);

        Route::put('/paragraph/{id}', [ParagraphController::class, 'update']);

        Route::delete('/paragraph/{id}', [ParagraphController::class, 'destroy']);
    });

    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);

    Route::delete('/logout', [AuthenticatedSessionController::class, 'destroy']);

    Route::post('/reset-password', [NewPasswordController::class, 'store']);

    Route::get('/savedpost', [SavedPostController::class, 'index']);
    Route::post('/savedpost', [SavedPostController::class, 'store']);

    Route::get('/comment', [CommentController::class, 'index']);
    Route::get('/comment/{id}', [CommentController::class, 'show']);
    Route::post('/comment', [CommentController::class, 'store']);
    Route::put('/comment/{id}', [CommentController::class, 'update']);
    Route::delete('/comment/{id}', [CommentController::class, 'destroy']);

    Route::get('/answer', [AnswerController::class, 'index']);
    Route::get('/answer/{id}', [AnswerController::class, 'show']);
    Route::post('/answer', [AnswerController::class, 'store']);
    Route::put('/answer/{id}', [AnswerController::class, 'update']);
    Route::delete('/answer/{id}', [AnswerController::class, 'destroy']);
});

Route::get('/user', [UserController::class, 'index']);

Route::get('/user/{id}', [UserController::class, 'show']);

Route::post('/user', [UserController::class, 'store']);

Route::post('/login', [AuthenticatedSessionController::class, 'store']);

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);

Route::get('/post', [PostController::class, 'index']);
Route::get('/post/{id}', [PostController::class, 'show']);

Route::get('/paragraph', [ParagraphController::class, 'index']);
Route::get('/paragraph/{id}', [ParagraphController::class, 'show']);
