<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    // POST /api/ratings/{recipe_id} — beri rating ke resep
    public function store(Request $request, $recipeId)
    {
        $recipe = Recipe::find($recipeId);

        if (!$recipe) {
            return response()->json(['status' => false, 'message' => 'Resep tidak ditemukan'], 404);
        }

        if ($recipe->user_id === $request->user()->id) {
            return response()->json(['status' => false, 'message' => 'Tidak bisa memberi rating resep sendiri'], 403);
        }

        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        // updateOrCreate: kalau sudah pernah rating, update; kalau belum, buat baru
        $rating = Rating::updateOrCreate(
            [
                'user_id'   => $request->user()->id,
                'recipe_id' => $recipeId,
            ],
            [
                'rating'  => $request->rating,
                'comment' => $request->comment,
            ]
        );

        $averageRating = round(Rating::where('recipe_id', $recipeId)->avg('rating'), 1);

        return response()->json([
            'status'         => true,
            'message'        => 'Rating berhasil disimpan',
            'data'           => $rating,
            'average_rating' => $averageRating
        ]);
    }

    // GET /api/ratings/{recipe_id} — daftar semua rating sebuah resep
    public function index($recipeId)
    {
        $recipe = Recipe::find($recipeId);

        if (!$recipe) {
            return response()->json(['status' => false, 'message' => 'Resep tidak ditemukan'], 404);
        }

        $ratings = Rating::with('user:id,name')
            ->where('recipe_id', $recipeId)
            ->latest()
            ->get();

        $averageRating = round($ratings->avg('rating'), 1);

        return response()->json([
            'status'         => true,
            'message'        => 'Daftar Rating',
            'average_rating' => $averageRating,
            'total'          => $ratings->count(),
            'data'           => $ratings
        ]);
    }
}
