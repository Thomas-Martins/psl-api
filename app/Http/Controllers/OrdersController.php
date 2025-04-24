<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Resources\OrderResource;
use App\Models\Carrier;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
        ]);

        $ordersQuery = Order::query();

        if (! empty($validated['user_id'])) {
            $ordersQuery->where('user_id', $validated['user_id']);
        }

        $orders = $ordersQuery
            ->orderBy('created_at', 'desc');

        $orders = PaginationHelper::paginateIfAsked($orders);

        return OrderResource::collection($orders)
            ->response()
            ->setStatusCode(200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'complementary_info' => 'nullable|string',
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                // 1. Create the command
                $products = Product::whereIn('id', collect($validated['products'])->pluck('id'))->get()->keyBy('id');
                $totalPrice = collect($validated['products'])
                    ->sum(fn($item) => $products[$item['id']]->price * $item['quantity']);


                $order = Order::create([
                    'user_id' => $validated['user_id'],
                    'carrier_id' => null,
                    'status' => Order::STATUS_PENDING,
                    'estimated_delivery_date' => null,
                    'departure_date' => null,
                    'arrival_date' => null,
                    'total_price' => $totalPrice,
                    'cancellation_reason' => null,
                    'notes' => $validated['complementary_info'] ?? null,
                    'reference' => '',
                ]);

                $datePart  = now()->format('Ymd');
                $number    = str_pad($order->id, 4, '0', STR_PAD_LEFT);
                $order->reference = "{$datePart}-{$number}";
                $order->save();

                // 2. Create command products
                foreach ($validated['products'] as $product) {
                    $order->ordersProducts()->create([
                        'product_id' => $product['id'],
                        'quantity' => $product['quantity'],
                        'freeze_price' => $products[$product['id']]->price,
                    ]);

                    // 3. Update product stock
                    $productModel = $products[$product['id']];
                    $productModel->stock -= $product['quantity'];
                    if ($productModel->stock < 0) {
                        return response()->json([
                            'message' => 'Not enough stock for product: ' . $productModel->name,
                        ], 422);
                    }
                    $productModel->save();
                }

                return response()->json([
                    'message' => 'Order created successfully',
                    'data'    => $order->load('ordersProducts'),
                ], 201);


            });
        }catch (\Exception $exception){
            return response()->json([
                'message' => 'An error occurred while creating the order',
                'error' => $exception->getMessage(),
            ], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        if(!$order->exists) {
            return response()->json([
                'message' => 'Order not found',
            ], 404);
        }
        if($order->user_id !== auth()->user()->id && auth()->user()->role !== Role::ADMIN && auth()->user()->role !== Role::GESTIONNAIRE) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $order->load('ordersProducts.product');

        return (new OrderResource($order))
            ->additional(['message' => 'Order retrieved successfully'])
            ->response()
            ->setStatusCode(200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }

    public function printOrder(Order $order){

    }
}
