<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Requests\CreateStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Models\Store;
use Illuminate\Http\Request;

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

        $store = Store::create($data);

        return response()->json(['message' => 'Store created', 'carrier' => $store], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Store $store)
    {
        return $store;
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
