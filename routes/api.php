<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\RatingController;

// =============================================
//  PUBLIC ROUTES (tidak butuh login)
// =============================================

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Kategori (publik — bisa dilihat tanpa login)
Route::get('/categories',      [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// Resep (publik — bisa browse tanpa login)
Route::get('/recipes',      [RecipeController::class, 'index']);
Route::get('/recipes/image/{filename}', [RecipeController::class, 'serveImage']);
Route::get('/recipes/{id}', [RecipeController::class, 'show']);

// Rating (publik — bisa lihat rating tanpa login)
Route::get('/ratings/{recipe_id}', [RatingController::class, 'index']);

// =============================================
//  PROTECTED ROUTES (butuh token Sanctum)
// =============================================
Route::middleware('auth:sanctum')->group(function () {

    // --- AUTH ---
    Route::post('/logout',          [AuthController::class, 'logout']);
    Route::get('/profile',          [AuthController::class, 'profile']);
    Route::put('/profile',          [AuthController::class, 'updateProfile']);

    // --- RESEP (CRUD — hanya pemilik) ---
    Route::post('/recipes',         [RecipeController::class, 'store']);
    Route::put('/recipes/{id}',     [RecipeController::class, 'update']);
    Route::delete('/recipes/{id}',  [RecipeController::class, 'destroy']);
    Route::get('/my-recipes',       [RecipeController::class, 'myRecipes']);

    // --- KATEGORI (Admin Only) ---
    Route::post('/categories',         [CategoryController::class, 'store']);
    Route::put('/categories/{id}',     [CategoryController::class, 'update']);
    Route::delete('/categories/{id}',  [CategoryController::class, 'destroy']);

    // --- FAVORIT ---
    Route::get('/favorites',                        [FavoriteController::class, 'index']);
    Route::post('/favorites/{recipe_id}/toggle',    [FavoriteController::class, 'toggle']);
    Route::get('/favorites/{recipe_id}/check',      [FavoriteController::class, 'check']);

    // --- RATING ---
    Route::post('/ratings/{recipe_id}', [RatingController::class, 'store']);
});