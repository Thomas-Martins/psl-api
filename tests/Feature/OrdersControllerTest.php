<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for OrdersController endpoints.
 *
 * This class covers:
 * - Listing orders
 * - Showing order details
 * - Creating orders
 * - Updating order status
 * - Downloading invoices (with role-based access)
 */
class OrdersControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Role $adminRole Admin role instance
     */
    protected Role $adminRole;
    /**
     * @var Role $userRole Logistician role instance
     */
    protected Role $userRole;
    /**
     * @var Role $clientRole Client role instance
     */
    protected Role $clientRole;
    /**
     * @var Role $gestionnaireRole Manager role instance
     */
    protected Role $gestionnaireRole;

    /**
     * Set up roles before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $this->adminRole = Role::create(['name' => 'admin']);
        $this->gestionnaireRole = Role::create(['name' => 'gestionnaire']);
        $this->userRole = Role::create(['name' => 'logisticien']);
        $this->clientRole = Role::create(['name' => 'client']);
    }

    /**
     * Test that the orders list endpoint returns a 200 status for a logistician user.
     */
    public function test_index_returns_orders_list()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create(['role_id' => $this->userRole->id]);
        $this->actingAs($user, 'api');
        $response = $this->getJson('/api/orders');
        $response->assertStatus(200);
    }

    /**
     * Test that an authenticated user can view the details of their order.
     */
    public function test_show_returns_order_details()
    {
        $store = Store::factory()->create();
        /** @var \App\Models\User $user */
        $user = User::factory()->create(['role_id' => $this->userRole->id, 'store_id' => $store->id]);

        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
        ]);

        $order = Order::factory()->create(['user_id' => $user->id]);
        $order->ordersProducts()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'freeze_price' => $product->price,
        ]);

        $this->actingAs($user, 'api');
        $response = $this->getJson("/api/orders/{$order->id}");
        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'message']);
    }

    /**
     * Test that a client user can create an order and receives a 201 response.
     */
    public function test_store_creates_order()
    {
        $store = Store::factory()->create();
        /** @var User $user */
        $user = User::factory()->create(['role_id' => $this->clientRole->id, 'store_id' => $store->id]);
        $product = Product::factory()->create(['stock' => 10, 'price' => 100]);
        $payload = [
            'user_id' => $user->id,
            'products' => [
                [
                    'id' => $product->id,
                    'quantity' => 2,
                    'price' => $product->price,
                ]
            ],
            'complementary_info' => 'Test info',
        ];
        $this->actingAs($user, 'api');
        $response = $this->postJson('/api/orders', $payload);
        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'data']);
    }

    /**
     * Test that an admin user can update the status of an order.
     */
    public function test_update_order_status()
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $store = Store::factory()->create();
        $user = User::factory()->create(['role_id' => $this->clientRole->id, 'store_id' => $store->id]);
        $product = Product::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => Order::STATUS_PENDING]);
        $order->ordersProducts()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'freeze_price' => $product->price,
        ]);
        $payload = ['status' => Order::STATUS_COMPLETED];
        $this->actingAs($admin, 'api');
        $response = $this->putJson("/api/orders/{$order->id}", $payload);
        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Order updated successfully']);
    }

    /**
     * Test that a client user cannot download the invoice for another user's order (should return 403).
     */
    public function test_download_invoice_unauthorized_for_other_user()
    {
        $store = Store::factory()->create();

        /** @var User $user */
        $user = User::factory()->create(['role_id' => $this->clientRole->id, 'store_id' => $store->id]);

        /** @var User $otherUser */
        $otherUser = User::factory()->create(['role_id' => $this->clientRole->id, 'store_id' => $store->id]);

        $product = Product::factory()->create();
        $order = Order::factory()->create(['user_id' => $otherUser->id]);
        $order->ordersProducts()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'freeze_price' => $product->price,
        ]);

        $this->actingAs($user, 'api');
        $response = $this->getJson("/api/orders/{$order->id}/invoice");
        $response->assertStatus(403);
    }

    /**
     * Test that the owner of an order can download the invoice (should return 200).
     */
    public function test_download_invoice_authorized_for_owner()
    {
        $store = Store::factory()->create();
        /** @var User $user */
        $user = User::factory()->create(['role_id' => $this->clientRole->id, 'store_id' => $store->id]);
        $product = Product::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $order->ordersProducts()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'freeze_price' => $product->price,
        ]);

        $this->actingAs($user, 'api');
        $response = $this->get("/api/orders/{$order->id}/invoice");
        $response->assertStatus(200);
    }

    /**
     * Test that an admin user can download the invoice for any order (should return 200).
     */
    public function test_download_invoice_authorized_for_admin()
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $store = Store::factory()->create();
        $user = User::factory()->create(['role_id' => $this->clientRole->id, 'store_id' => $store->id]);

        $product = Product::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $order->ordersProducts()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'freeze_price' => $product->price,
        ]);

        $this->actingAs($admin, 'api');
        $response = $this->get("/api/orders/{$order->id}/invoice");
        $response->assertStatus(200);
    }

    /**
     * Test that a manager user can download the invoice for any order (should return 200).
     */
    public function test_download_invoice_authorized_for_gestionnaire()
    {
        /** @var \App\Models\User $gestionnaire */
        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);

        $store = Store::factory()->create();
        $user = User::factory()->create(['role_id' => $this->clientRole->id, 'store_id' => $store->id]);

        $product = Product::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $order->ordersProducts()->create([
            'product_id' => $product->id,
            'quantity' => 1,
            'freeze_price' => $product->price,
        ]);

        $this->actingAs($gestionnaire, 'api');
        $response = $this->get("/api/orders/{$order->id}/invoice");
        $response->assertStatus(200);
    }
}
