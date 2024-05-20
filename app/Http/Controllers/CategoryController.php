<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name', 'asc')->get(); // Mengurutkan kategori berdasarkan nama secara ascending
        return response()->json($categories);
    }
    

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    $category = Category::create($request->all());
    return response()->json([
        'message' => 'Category berhasil ditambahkan',
        'category' => $category,
    ], 201);
}

    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
        ]);

        $category->update($request->all());

    return response()->json([
        'message' => 'Category updated successfully',
        'category' => $category,
    ]);
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
