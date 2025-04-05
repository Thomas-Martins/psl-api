<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Requests\CreateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::query();

        return PaginationHelper::paginateIfAsked($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCategoryRequest $request)
    {
        $data = $request->validated();

        try {
            $category = Category::create($data);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la création de la catégorie'], 500);
        }

        return response()->json(['message' => 'Category created', 'category' => $category], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $category;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();

        try {
            $category->update($data);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la mise à jour de la catégorie'], 500);
        }

        return response()->json(['message' => 'Category updated', 'category' => $category], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            $category->delete();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la suppression de la catégorie'], 500);
        }

        return response()->json(['message' => 'Category deleted'], 200);
    }
}
