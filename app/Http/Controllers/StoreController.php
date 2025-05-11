<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Requests\CreateStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Http\Resources\StoreResource;
use App\Models\Role;
use App\Models\Store;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StoreController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $stores = Store::query();

        if ($request->filled('search')) {
            $search = $request->input('search');

            $stores->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $stores->withCount('customers');

        return PaginationHelper::paginateIfAsked($stores);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateStoreRequest $request)
    {
        $data = $request->validated();

        if(Auth::user()->role !== Role::ADMIN && Auth::user()->role !== Role::GESTIONNAIRE) {
            return response()->json(['message' => 'Unauthorized'], 405);
        }

        try {
            if (isset($data['image']) && !is_null($data['image'])) {
                $data['image_path'] = (new ImageUploadService())->upload($data['image'], 'stores', 'store');
            }

            $store = Store::create($data);

        }catch (\Exception $exception){

            if (isset($data['image_path'])) {
                Storage::disk('public')->delete($data['image_path']);
            }

            return response()->json(['message' => 'Erreur lors de la création du point de vente'], 500);
        }

        return response()->json(['message' => 'Store created', 'carrier' => $store], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        return StoreResource::make($store);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStoreRequest $request, Store $store)
    {
        $data = $request->validated();

        $store->update($data);

        return response()->json(['message' => 'Store updated', 'store' => $store]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Store $store)
    {
        $store->delete();
        return response()->noContent();
    }
}
