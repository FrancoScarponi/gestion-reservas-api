<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkspaceController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register',[UserController::class,'register'])->name('register');
Route::post('/login', [UserController::class,'login'])->name('login');

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/user',[UserController::class,'checkAuth'])->name('');

    Route::apiResource('workspace', WorkspaceController::class);
});