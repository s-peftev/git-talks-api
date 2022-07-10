<?php

use App\Http\Controllers\FollowingController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);
Route::post('/auth/me', [LoginController::class, 'authMe']);

Route::get('/users', [UserController::class, 'index'])
    ->middleware('auth:sanctum');
Route::get('/users/{id}', [UserController::class, 'show'])
    ->middleware('auth:sanctum');

Route::post('/follow/{id}', [FollowingController::class, 'follow'])
    ->middleware('auth:sanctum');
Route::delete('/follow/{id}', [FollowingController::class, 'unfollow'])
    ->middleware('auth:sanctum');
