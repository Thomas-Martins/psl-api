<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Requests\CreateProductsRequest;
use App\Http\Requests\UpdateProductsRequest;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Services\ImageUploadService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ProductsController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): LengthAwarePaginator|Collection
    {
        $validated = $request->validate([
            'search'           => 'sometimes|string',
            'priceRange'       => 'sometimes|array|size:2',
            'priceRange.*'     => 'sometimes|numeric|min:0',
        ]);

        $products = Product::query()->with(['category', 'supplier']);

        if (!empty($validated['search'])) {
            $products->where('name', 'like', '%'.$validated['search'].'%');
        }

        if ($request->filled('categories')) {
            $catsParam = $request->input('categories');

            if (is_string($catsParam)) {
                $names = explode(',', $catsParam);
            } elseif (is_array($catsParam)) {
                $names = $catsParam;
            } else {
                $names = [];
            }

            $names = array_filter(array_map('trim', $names));

            if (!empty($names)) {
                $ids = Category::whereIn('name', $names)->pluck('id');
                $products->whereIn('category_id', $ids);
            }
        }

        if (!empty($validated['priceRange'])) {
            [$min, $max] = $validated['priceRange'];

            if ($min > $max) {
                [$min, $max] = [$max, $min];
            }

            $products->whereBetween('price', [$min, $max]);
        }

        $pagination = PaginationHelper::paginateIfAsked($products);
        $pagination->getCollection()->transform(fn($p) => new ProductResource($p));

        return $pagination;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProductsRequest $request): JsonResponse
    {
        $data = $request->validated();

        if(Auth::user()->role !== Role::ADMIN && Auth::user()->role !== Role::GESTIONNAIRE) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            if (isset($data['image']) && !is_null($data['image'])) {
                $data['image_path'] = (new ImageUploadService())->upload($data['image'], 'products', 'product');
            }
            $product = Product::create($data);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la création du produit'], 500);
        }

        return response()->json(['message' => 'Product created', 'product' => $product], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductsRequest $request, Product $product): JsonResponse
    {
        $data = $request->validated();

        if(Auth::user()->role !== Role::ADMIN && Auth::user()->role !== Role::GESTIONNAIRE) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            if (isset($data['image']) && !is_null($data['image'])) {
                $data['image_path'] = (new ImageUploadService())->upload($data['image'], 'products', 'product');
            }
            $product->update($data);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la mise à jour du produit'], 500);
        }

        return response()->json(['message' => 'Product updated', 'product' => $product], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        if(Auth::user()->role !== Role::ADMIN && Auth::user()->role !== Role::GESTIONNAIRE) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $product->delete();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur lors de la suppression du produit'], 500);
        }

        return response()->json(['message' => 'Product deleted'], 200);
    }
}
