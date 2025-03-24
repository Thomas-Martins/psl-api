<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Requests\Suppliers\CreateSupplierRequest;
use App\Http\Requests\Suppliers\UpdateSupplierRequest;
use App\Models\Role;
use App\Models\Supplier;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuppliersController
{
    /**
     * Display a listing of the resource.
     * Available for admin and gestionnaire users only.
     *
     */
    public function index(Request $request)
    {
        $suppliers = Supplier::query();

        if ($request->filled('search')) {
            $search = $request->input('search');

            $suppliers->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

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

        try {
            if (isset($data['image']) && !is_null($data['image'])) {
                $data['image_path'] = (new ImageUploadService())->upload($data['image'], 'suppliers', 'supplier');
            }

            Supplier::create($data);

        }catch (\Exception $e) {

            if(isset($data['image_path'])) {
                Storage::disk('public')->delete($data['image_path']);
            }

            return response()->json(['message' => 'Erreur lors de la création du fournisseur'], 500);
        }


        return response()->json(['message' => 'Supplier created'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier): Supplier
    {
        return $supplier;
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        $data = $request->validated();

        $supplier->update($data);

        return response()->json(['message' => 'Supplier updated', 'supplier' => $supplier], 200);
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

        return response()->noContent();
    }
}
