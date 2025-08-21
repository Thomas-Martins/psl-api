<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Http\Resources\OrderResource;
use App\Jobs\SendOrderConfirmationEmail;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Services\InvoiceService;
use App\Services\OrderDocumentService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Orders\CreateOrderRequest;
use App\Models\Carrier;
use Illuminate\Support\Facades\Log;

class OrdersController
{
    public function __construct(
        private InvoiceService $invoiceService,
        private OrderDocumentService $orderDocumentService
    ) {}

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
        } else {
            $ordersQuery->with('user.store');
        }

        if ($request->filled('search')) {
            $search = $request->input('search');

            $ordersQuery->where(function ($query) use ($search) {
                $query->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('firstname', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhereHas('store', function ($q) use ($search) {
                                $q->where('address', 'like', "%{$search}%")
                                    ->orWhere('city', 'like', "%{$search}%")
                                    ->orWhere('zipcode', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $orders = $ordersQuery
            ->orderBy('created_at', 'desc');

        $orders = PaginationHelper::paginateIfAsked($orders);
        if ($orders instanceof LengthAwarePaginator) {

            $statusLabels = (new Order())->statusLabels();
            $orders->getCollection()->transform(fn($o) => new OrderResource($o));

            return response()->json([
                'data' => $orders->items(),
                'links' => $orders->linkCollection()->toArray(),
                'total' => $orders->total(),
                'status' => $statusLabels,
            ]);
        }


        return $orders->transform(fn($o) => new OrderResource($o));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrderRequest $request)
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

                $randomCarrierId = Carrier::inRandomOrder()->value('id');

                $order = Order::create([
                    'user_id'                 => $user->id,
                    'carrier_id'              => $randomCarrierId,
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

            $locale = $request->input('locale') ?? $request->query('locale') ?? config('app.locale');

            if (!in_array($locale, ['fr', 'en'])) {
                $locale = config('app.locale', 'fr');
            }

            try {
                SendOrderConfirmationEmail::dispatch($order, $locale);
            } catch (\Exception $e) {
                Log::error('Failed to send order confirmation email', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
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
        if (!$order->exists) {
            return response()->json([
                'message' => 'Order not found',
            ], 404);
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
        if (
            Auth::user()->role !== Role::ADMIN &&
            Auth::user()->role !== Role::GESTIONNAIRE
        ) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', Order::STATUS_VALUES),
        ]);

        $oldStatus = $order->status;
        $newStatus = $validated['status'];


        if (
            $oldStatus === Order::STATUS_PENDING &&
            ($newStatus === Order::STATUS_PROCESSING || $newStatus === Order::STATUS_COMPLETED || $newStatus === Order::STATUS_SHIPPED)
        ) {
            $order->estimated_delivery_date = now()->addWeek();
        }

        if ($oldStatus === Order::STATUS_PROCESSING && $newStatus === Order::STATUS_PENDING) {
            $order->estimated_delivery_date = null;
        }

        $order->update($validated);

        return response()->json([
            'message' => 'Order updated successfully',
            'data' => new OrderResource($order),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }

    public function downloadInvoice(Request $request, Order $order)
    {
        if (
            $order->user_id !== Auth::user()->id &&
            Auth::user()->role !== Role::ADMIN &&
            Auth::user()->role !== Role::GESTIONNAIRE &&
            Auth::user()->role !== Role::LOGISTICIEN
        ) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $locale = $request->input('locale') ?? $request->query('locale') ?? $request->header('Accept-Language');
        $order->load(['ordersProducts.product', 'user.store']);

        try {
            return $this->invoiceService->generatePdfDownload($order, $locale);
        } catch (\Exception $e) {
            Log::error('Invoice download failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'message' => 'Failed to generate invoice',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function downloadProductsList(Request $request, Order $order)
    {
        if (
            $order->user_id !== Auth::user()->id &&
            Auth::user()->role !== Role::ADMIN &&
            Auth::user()->role !== Role::GESTIONNAIRE &&
            Auth::user()->role !== Role::LOGISTICIEN
        ) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $order->load(['ordersProducts.product', 'user.store']);

        $locale = $request->input('locale') ?? $request->query('locale') ?? $request->header('Accept-Language');

        try {
            return $this->orderDocumentService->generateProductsListDownload($order, $locale);
        } catch (\Exception $e) {
            Log::error('Products list download failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'message' => 'Failed to generate products list',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
