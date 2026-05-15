<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // GET /api/categories — semua kategori
    public function index()
    {
        $categories = Category::withCount('recipes')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Daftar Kategori',
            'data'    => $categories
        ]);
    }

    // GET /api/categories/{id} — detail kategori + resep-resepnya
    public function show($id)
    {
        $category = Category::with(['recipes.user', 'recipes.ratings'])->find($id);

        if (!$category) {
            return response()->json(['status' => false, 'message' => 'Kategori tidak ditemukan'], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Detail Kategori',
            'data'    => $category
        ]);
    }
}
