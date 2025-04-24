<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'products'               => 'required|array',
            'products.*.id'          => 'required|exists:products,id',
            'products.*.quantity'    => 'required|integer|min:1',
        ]);

        $user = $request->user();

        $cart = DB::transaction(function () use ($user, $validated) {
            $cart = Cart::firstOrCreate([
                'user_id' => $user->id,
            ]);

            $syncData = collect($validated['products'])
                ->mapWithKeys(fn($p) => [
                    $p['id'] => ['quantity' => $p['quantity']],
                ])
                ->all();

            $cart->products()->sync($syncData);

            return $cart->load('products');
        });

        $status = $cart->wasRecentlyCreated ? 201 : 200;

        return response()->json($cart, $status);
    }

    public function showCartUser($userId)
    {
        $cart = Cart::where('user_id', $userId)
            ->with('products')
            ->first();

        if (!$cart) {
            return response()->json(['products' => []], 200);
        }

        return new CartResource($cart);
    }
}
