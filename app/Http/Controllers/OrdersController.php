<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Resources\OrderResource;
use App\Models\Carrier;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Orders\CreateOrderRequest;
use App\Mail\OrderConfirmation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

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
    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            $order = DB::transaction(function () use ($request) {
                $products = Product::whereIn('id', collect($request->validated()['products'])->pluck('id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($request->validated()['products'] as $item) {
                    $model = $products[$item['id']];
                    if ($model->stock < $item['quantity']) {
                        throw new \Exception("Not enough stock for product: {$model->name}");
                    }
                }

                // Get user's store for shipping address
                $user = \App\Models\User::with('store')->findOrFail($request->validated()['user_id']);
                
                $totalHt = collect($request->validated()['products'])
                    ->sum(fn($item) => $item['price'] * $item['quantity']);

                $order = Order::create([
                    'user_id'                 => $user->id,
                    'carrier_id'              => null,
                    'status'                  => Order::STATUS_PENDING,
                    'estimated_delivery_date' => null,
                    'departure_date'          => null,
                    'arrival_date'            => null,
                    'total_price'             => $totalHt,
                    'cancellation_reason'     => null,
                    'notes'                   => $request->validated()['complementary_info'] ?? null,
                    'reference'               => '',
                ]);

                $datePart = now()->format('Ymd');
                $number   = str_pad($order->id, 4, '0', STR_PAD_LEFT);
                $order->reference = "{$datePart}-{$number}";
                $order->save();

                foreach ($request->validated()['products'] as $item) {
                    $order->ordersProducts()->create([
                        'product_id'   => $item['id'],
                        'quantity'     => $item['quantity'],
                        'freeze_price' => $item['price'],
                    ]);

                    $products[$item['id']]->decrement('stock', $item['quantity']);
                }

                return $order;
            });

            // Load relationships needed for the email
            $order->load(['ordersProducts', 'user.store']);

            // Send confirmation email and handle failures separately
            try {
                Mail::to($order->user->email)->send(new OrderConfirmation(
                    order: $order,
                    userLocale: $request->input('locale', config('app.locale'))
                ));
            } catch (\Exception $e) {
                Log::error('Order confirmation email failed: ' . $e->getMessage());
                // Continue execution - don't let email failure affect the API response
            }

            return response()->json([
                'message' => 'Order created successfully',
                'data'    => $order->load('ordersProducts'),
            ], 201);

        } catch (\Exception $e) {
            Log::error('Order creation failed: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            $status = str_contains($e->getMessage(), 'Not enough stock') ? 422 : 500;
            return response()->json([
                'message' => $status === 422
                    ? $e->getMessage()
                    : 'An error occurred while creating the order',
                'error'   => $status === 500 ? $e->getMessage() : null,
            ], $status);
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
        if($order->user_id !== Auth::user()->id && Auth::user()->role !== Role::ADMIN && Auth::user()->role !== Role::GESTIONNAIRE) {
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
