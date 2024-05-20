<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; // Import for custom validation

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function store(ProductRequest $request)
    {
        $validatedData = $request->validated();

        // Custom validation using Validator facade (more flexible)
        $validator = Validator::make($validatedData, [
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422); // Unprocessable Entity
        }

        $validatedData['modified_by'] = Auth::id();

        $product = Product::create($validatedData);
        return response()->json(['message' => 'Product created successfully', 'product' => $product], 201);
    }

    public function update(ProductRequest $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $validatedData = $request->validated();

        if (isset($validatedData['category_id'])) {
            $category = Category::find($validatedData['category_id']);
            if (!$category) {
                return response()->json(['error' => 'Category not found'], 404);
            }
        }

        $validatedData['modified_by'] = Auth::id();

        $product->update($validatedData);
        return response()->json(['message' => 'Product updated successfully', 'product' => $product]);
    }

    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}