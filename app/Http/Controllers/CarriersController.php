<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Requests\Carriers\CreateCarriersRequest;
use App\Http\Requests\Carriers\UpdateCarrierRequest;
use App\Models\Carrier;
use App\Models\Role;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CarriersController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $carriers = Carrier::query();

        if ($request->filled('search')) {
            $search = $request->input('search');

            $carriers->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return PaginationHelper::paginateIfAsked($carriers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCarriersRequest $request)
    {
        $data = $request->validated();

        if(Auth::user()->role !== Role::ADMIN && Auth::user()->role !== Role::GESTIONNAIRE) {
            return response()->json(['message' => 'Unauthorized'], 405);
        }

        try {
            if (isset($data['image']) && !is_null($data['image'])) {
                $data['image_path'] = (new ImageUploadService())->upload($data['image'], 'carriers', 'carrier');
            }

            $carrier = Carrier::create($data);

        }catch (\Exception $e) {
            if (isset($data['image_path'])) {
                Storage::disk('public')->delete($data['image_path']);
            }

            return response()->json(['message' => 'Erreur lors de la création du transporteur'], 500);
        }


        return response()->json(['message' => 'Carrier created', 'carrier' => $carrier], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Carrier $carrier): Carrier
    {
        return $carrier;
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCarrierRequest $request, Carrier $carrier)
    {
        $data = $request->validated();

        $carrier->update($data);

        return response()->json(['message' => 'Carrier updated', 'carrier' => $carrier], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Carrier $carrier)
    {
        $carrier->delete();
        return response()->noContent();
    }
}
