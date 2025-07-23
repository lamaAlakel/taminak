<?php
// File: app/Http/Controllers/Admin/CategoryController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // GET /api/admin/categories
    public function index()
    {
        return response()->json(Category::all());
    }

    // POST /api/admin/categories
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|url',
        ]);

        $category = Category::create($data);
        return response()->json($category, 201);
    }

    // GET /api/admin/categories/{category}
    public function show(Category $category)
    {
        return response()->json($category);
    }

    // PUT/PATCH /api/admin/categories/{category}
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'image' => 'sometimes|required|url',
        ]);

        $category->update($data);
        return response()->json($category, 200);
    }

    // DELETE /api/admin/categories/{category}
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(null, 204);
    }
}
