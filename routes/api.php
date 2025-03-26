<?php

use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Resources\ReservationResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Public routes
Route::post('/register', [UserController::class, 'register'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');

//Rutas con auth
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'checkAuth'])->name('');

    Route::apiResource('workspace', WorkspaceController::class);

    Route::apiResource('reservation', ReservationController::class)->except(['show', 'update']);
    Route::get('reservation/schedule', [ReservationController::class, 'getSchedule'])->name('reservation.schedule');

    //Rutas de admin
    Route::middleware('role:admin')->group(function () {
        Route::get('reservation/pending', [ReservationController::class, 'indexPending'])->name('reservation.pending');
        Route::put('reservation/{id}', [ReservationController::class, 'changeStatus'])->name('reservation.changeStatus');
    });
});
