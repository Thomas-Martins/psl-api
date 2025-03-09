<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Requests\Suppliers\CreateSupplierRequest;
use App\Models\Role;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuppliersController
{
    /**
     * Display a listing of the resource.
     * Available for admin and gestionnaire users only.
     *
     */
    public function index(Request $request)
    {
        if(Auth::user()->role !== Role::ADMIN && Auth::user()->role !== Role::GESTIONNAIRE) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $suppliers = Supplier::query();

        return PaginationHelper::paginateIfAsked($suppliers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSupplierRequest $request)
    {
        $data = $request->validated();

        if(Auth::user()->role !== Role::ADMIN && Auth::user()->role !== Role::GESTIONNAIRE) {
            return response()->json(['message' => 'Unauthorized'], 405);
        }

        Supplier::create($data);

        return response()->json(['message' => 'Supplier created'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        if(Auth::user()->role !== Role::ADMIN && Auth::user()->role !== Role::GESTIONNAIRE) {
            return response()->json(['message' => 'Unauthorized'], 405);
        }

        $supplier->delete();

        return response()->json(null, 204);
    }
}
