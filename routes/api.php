<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');

Route::middleware(AuthMiddleware::class)->group(function () {
    Route::post('/profile/image', [UserController::class, 'updateProfileImage'])->name('profile.image.set');
    Route::delete('/profile/image', [UserController::class, 'deleteProfileImage'])->name('profile.image.delete');
    Route::get('/users/{user}/posts', [UserController::class, 'posts'])->name('users.posts')->where('user', uuid7Regex());
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show')->where('post', uuid7Regex());
});
