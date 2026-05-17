<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Ingredient;
use App\Models\Step;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecipeController extends Controller
{
    // GET /api/recipes — semua resep (bisa filter by kategori / search)
    public function index(Request $request)
    {
        $query = Recipe::with(['user:id,name', 'category:id,name,icon', 'ratings'])
            ->withCount('favorites');

        // Filter by kategori
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Search by judul
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by difficulty
        if ($request->difficulty) {
            $query->where('difficulty', $request->difficulty);
        }

        $recipes = $query->latest()->paginate(10);

        // Tambah average_rating ke setiap resep
        $recipes->getCollection()->transform(function ($recipe) {
            $recipe->average_rating = $recipe->average_rating;
            return $recipe;
        });

        return response()->json([
            'status'  => true,
            'message' => 'Daftar Resep',
            'data'    => $recipes
        ]);
    }

    // GET /api/recipes/{id} — detail resep
    public function show($id)
    {
        $recipe = Recipe::with([
            'user:id,name',
            'category:id,name,icon',
            'ingredients',
            'steps',
            'ratings.user:id,name'
        ])->withCount('favorites')->find($id);

        if (!$recipe) {
            return response()->json(['status' => false, 'message' => 'Resep tidak ditemukan'], 404);
        }

        $recipe->average_rating = $recipe->average_rating;

        return response()->json([
            'status'  => true,
            'message' => 'Detail Resep',
            'data'    => $recipe
        ]);
    }

    // POST /api/recipes — buat resep baru (butuh login)
    public function store(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['status' => false, 'message' => 'Hanya Admin yang bisa menambah resep'], 403);
        }
        $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'video_url'     => 'nullable|url',
            'cooking_time'  => 'required|integer|min:1',
            'servings'      => 'required|integer|min:1',
            'difficulty'    => 'required|in:mudah,sedang,sulit',
            'ingredients'   => 'required|array|min:1',
            'ingredients.*.name'     => 'required|string',
            'ingredients.*.quantity' => 'required|string',
            'ingredients.*.unit'     => 'nullable|string',
            'steps'         => 'required|array|min:1',
            'steps.*.instruction'    => 'required|string',
            'steps.*.image'          => 'nullable|string',
        ]);

        // Handle upload gambar resep
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('recipes', 'public');
            $imagePath = asset('storage/' . $imagePath);
        }

        // Buat resep
        $recipe = Recipe::create([
            'user_id'      => $request->user()->id,
            'category_id'  => $request->category_id,
            'title'        => $request->title,
            'description'  => $request->description,
            'video_url'    => $request->video_url,
            'image'        => $imagePath,
            'cooking_time' => $request->cooking_time,
            'servings'     => $request->servings,
            'difficulty'   => $request->difficulty,
        ]);

        // Simpan bahan-bahan
        foreach ($request->ingredients as $item) {
            Ingredient::create([
                'recipe_id' => $recipe->id,
                'name'      => $item['name'],
                'quantity'  => $item['quantity'],
                'unit'      => $item['unit'] ?? null,
            ]);
        }

        // Simpan langkah-langkah
        foreach ($request->steps as $index => $step) {
            Step::create([
                'recipe_id'   => $recipe->id,
                'step_number' => $index + 1,
                'instruction' => $step['instruction'],
                'image'       => $step['image'] ?? null,
            ]);
        }

        $recipe->load(['ingredients', 'steps', 'category:id,name', 'user:id,name']);

        return response()->json([
            'status'  => true,
            'message' => 'Resep berhasil ditambahkan',
            'data'    => $recipe
        ], 201);
    }

    // PUT /api/recipes/{id} — update resep (hanya pemilik)
    public function update(Request $request, $id)
    {
        $recipe = Recipe::find($id);

        if (!$recipe) {
            return response()->json(['status' => false, 'message' => 'Resep tidak ditemukan'], 404);
        }

        if (!$request->user()->isAdmin()) {
            return response()->json(['status' => false, 'message' => 'Hanya Admin yang bisa mengubah resep'], 403);
        }

        $request->validate([
            'category_id'  => 'sometimes|exists:categories,id',
            'title'        => 'sometimes|string|max:255',
            'description'  => 'nullable|string',
            'video_url'    => 'nullable|url',
            'cooking_time' => 'sometimes|integer|min:1',
            'servings'     => 'sometimes|integer|min:1',
            'difficulty'   => 'sometimes|in:mudah,sedang,sulit',
        ]);

        // Handle upload gambar baru
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('recipes', 'public');
            $recipe->image = asset('storage/' . $imagePath);
        }

        $recipe->update($request->only([
            'category_id', 'title', 'description', 'video_url', 'cooking_time', 'servings', 'difficulty'
        ]));

        // Update ingredients jika dikirim
        if ($request->has('ingredients')) {
            $recipe->ingredients()->delete();
            foreach ($request->ingredients as $item) {
                Ingredient::create([
                    'recipe_id' => $recipe->id,
                    'name'      => $item['name'],
                    'quantity'  => $item['quantity'],
                    'unit'      => $item['unit'] ?? null,
                ]);
            }
        }

        // Update steps jika dikirim
        if ($request->has('steps')) {
            $recipe->steps()->delete();
            foreach ($request->steps as $index => $step) {
                Step::create([
                    'recipe_id'   => $recipe->id,
                    'step_number' => $index + 1,
                    'instruction' => $step['instruction'],
                    'image'       => $step['image'] ?? null,
                ]);
            }
        }

        $recipe->load(['ingredients', 'steps', 'category:id,name', 'user:id,name']);

        return response()->json([
            'status'  => true,
            'message' => 'Resep berhasil diupdate',
            'data'    => $recipe
        ]);
    }

    // DELETE /api/recipes/{id} — hapus resep (hanya pemilik)
    public function destroy(Request $request, $id)
    {
        $recipe = Recipe::find($id);

        if (!$recipe) {
            return response()->json(['status' => false, 'message' => 'Resep tidak ditemukan'], 404);
        }

        if (!$request->user()->isAdmin()) {
            return response()->json(['status' => false, 'message' => 'Hanya Admin yang bisa menghapus resep'], 403);
        }

        $recipe->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Resep berhasil dihapus'
        ]);
    }

    // GET /api/my-recipes — resep milik user yang login
    public function myRecipes(Request $request)
    {
        $recipes = Recipe::with(['category:id,name,icon', 'ratings'])
            ->withCount('favorites')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        $recipes->transform(function ($recipe) {
            $recipe->average_rating = $recipe->average_rating;
            return $recipe;
        });

        return response()->json([
            'status'  => true,
            'message' => 'Resep saya',
            'data'    => $recipes
        ]);
    }
}
