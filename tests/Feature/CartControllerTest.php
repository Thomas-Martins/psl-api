<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for CartController endpoints.
 *
 * This class covers:
 * - Viewing a user's cart
 * - Adding products to the cart
 */
class CartControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Role $clientRole Client role instance
     */
    protected Role $clientRole;

    /**
     * Set up the client role before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $this->clientRole = Role::create(['name' => 'client']);
    }

    /**
     * Test that the cart endpoint returns a 200 status for a client user.
     */
    public function test_index_returns_cart()
    {
        $store = Store::factory()->create();
        $user = User::factory()->create(['role_id' => $this->clientRole->id, 'store_id' => $store->id]);

        /** @var User $user */
        $this->actingAs($user, 'api');
        $response = $this->getJson('/api/carts/user/' . $user->id);
        $response->assertStatus(200);
    }

    /**
     * Test that a client user can add a product to their cart and receives a 201 response.
     */
    public function test_add_product_to_cart()
    {
        $store = Store::factory()->create();
        $user = User::factory()->create(['role_id' => $this->clientRole->id, 'store_id' => $store->id]);

        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        $product = Product::factory()->create([
            'supplier_id' => $supplier->id,
            'category_id' => $category->id,
        ]);

        /** @var User $user */
        $this->actingAs($user, 'api');
        $response = $this->postJson('/api/carts', [
            'products' => [
                [
                    'id' => $product->id,
                    'quantity' => 1
                ]
            ]
        ]);
        $response->assertStatus(201);

        // Verify the cart contains the product after POST
        $getResponse = $this->getJson('/api/carts/user/' . $user->id);
        $getResponse->assertStatus(200);
        $getResponse->assertJsonFragment(['id' => $product->id]);
    }

    /**
     * Test that adding a product with invalid quantity returns 422.
     */
    public function test_add_product_to_cart_invalid_quantity()
    {
        $store = Store::factory()->create();
        /** @var User $user */
        $user = User::factory()->create(['role_id' => $this->clientRole->id, 'store_id' => $store->id]);
        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create([
            'supplier_id' => $supplier->id,
            'category_id' => $category->id,
        ]);
        $this->actingAs($user, 'api');
        $response = $this->postJson('/api/carts', [
            'products' => [
                [
                    'id' => $product->id,
                    'quantity' => 0 // invalid quantity
                ]
            ]
        ]);
        $response->assertStatus(422);
    }

    /**
     * Test that adding a non-existent product returns 422.
     */
    public function test_add_nonexistent_product_to_cart()
    {
        $store = Store::factory()->create();
        /** @var User $user */
        $user = User::factory()->create(['role_id' => $this->clientRole->id, 'store_id' => $store->id]);
        $this->actingAs($user, 'api');
        $response = $this->postJson('/api/carts', [
            'products' => [
                [
                    'id' => 999999, // non-existent product
                    'quantity' => 1
                ]
            ]
        ]);
        $response->assertStatus(422);
    }
}
