<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// REGISTER (BELUM ADA DI CODE KAMU)
Route::post('/register', [AuthController::class, 'register']);

// LOGIN
Route::post('/login', [AuthController::class, 'login']);

// ROUTE YANG BUTUH LOGIN (SANCTUM)
Route::middleware('auth:sanctum')->group(function () {

    // LOGOUT
    Route::post('/logout', [AuthController::class, 'logout']);

    // AMBIL DATA USER YANG LOGIN
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

});