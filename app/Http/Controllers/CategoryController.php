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

    // POST /api/categories — buat kategori baru (butuh login admin)
    public function store(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['status' => false, 'message' => 'Hanya Admin yang bisa menambah kategori'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string'
        ]);

        $category = Category::create($request->only(['name', 'icon']));

        return response()->json([
            'status'  => true,
            'message' => 'Kategori berhasil ditambahkan',
            'data'    => $category
        ], 201);
    }

    // PUT /api/categories/{id} — update kategori
    public function update(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['status' => false, 'message' => 'Hanya Admin yang bisa mengubah kategori'], 403);
        }

        $category = Category::find($id);

        if (!$category) {
            return response()->json(['status' => false, 'message' => 'Kategori tidak ditemukan'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string'
        ]);

        $category->update($request->only(['name', 'icon']));

        return response()->json([
            'status'  => true,
            'message' => 'Kategori berhasil diupdate',
            'data'    => $category
        ]);
    }

    // DELETE /api/categories/{id} — hapus kategori
    public function destroy(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['status' => false, 'message' => 'Hanya Admin yang bisa menghapus kategori'], 403);
        }

        $category = Category::find($id);

        if (!$category) {
            return response()->json(['status' => false, 'message' => 'Kategori tidak ditemukan'], 404);
        }

        $category->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Kategori berhasil dihapus'
        ]);
    }
}
