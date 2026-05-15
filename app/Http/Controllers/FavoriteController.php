<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Recipe;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    // GET /api/favorites — daftar resep favorit user yang login
    public function index(Request $request)
    {
        $favorites = Favorite::with([
            'recipe.category:id,name,icon',
            'recipe.user:id,name',
            'recipe.ratings'
        ])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        $data = $favorites->map(function ($fav) {
            $recipe = $fav->recipe;
            $recipe->average_rating = $recipe->average_rating;
            return $recipe;
        });

        return response()->json([
            'status'  => true,
            'message' => 'Daftar Favorit',
            'data'    => $data
        ]);
    }

    // POST /api/favorites/{recipe_id} — tambah/hapus favorit (toggle)
    public function toggle(Request $request, $recipeId)
    {
        $recipe = Recipe::find($recipeId);

        if (!$recipe) {
            return response()->json(['status' => false, 'message' => 'Resep tidak ditemukan'], 404);
        }

        $existing = Favorite::where('user_id', $request->user()->id)
            ->where('recipe_id', $recipeId)
            ->first();

        if ($existing) {
            // Kalau sudah ada → hapus (unfavorite)
            $existing->delete();
            return response()->json([
                'status'      => true,
                'message'     => 'Resep dihapus dari favorit',
                'is_favorite' => false
            ]);
        } else {
            // Belum ada → tambah (favorite)
            Favorite::create([
                'user_id'   => $request->user()->id,
                'recipe_id' => $recipeId
            ]);
            return response()->json([
                'status'      => true,
                'message'     => 'Resep ditambahkan ke favorit',
                'is_favorite' => true
            ]);
        }
    }

    // GET /api/favorites/{recipe_id}/check — cek apakah resep sudah difavoritkan
    public function check(Request $request, $recipeId)
    {
        $isFavorite = Favorite::where('user_id', $request->user()->id)
            ->where('recipe_id', $recipeId)
            ->exists();

        return response()->json([
            'status'      => true,
            'is_favorite' => $isFavorite
        ]);
    }
}
