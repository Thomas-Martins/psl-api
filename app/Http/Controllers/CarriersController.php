<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Requests\Carriers\CreateCarriersRequest;
use App\Http\Requests\Carriers\UpdateCarrierRequest;
use App\Models\Carrier;
use Illuminate\Http\Request;

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

        $carrier = Carrier::create($data);

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
        return response()->json(['message' => 'Carrier deleted'], 204);
    }
}
