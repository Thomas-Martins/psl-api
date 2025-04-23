<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductsControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_index_products(): void
    {
        Product::factory()->count(5)->create();
        $role = Role::create(['name' => Role::CLIENT]);
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user, 'api')->json('GET', '/api/products?paginate=true');

        $response->assertStatus(200);

        $response->assertJsonCount(5, 'data');
    }

    public function test_show_product(): void
    {
        $product = Product::factory()->create();
        $role = Role::create(['name' => Role::CLIENT]);
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user, 'api')->json('GET', '/api/products/' . $product->id);

        $response->assertStatus(200);
        $this->assertEquals($product->id, $response->json('id'));
    }

    public function test_store_admin_user(): void
    {
        $role = Role::create(['name' => Role::ADMIN]);
        $user = User::factory()->create(['role_id' => $role->id]);
        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'reference' => 'TEST123',
            'location' => 'Test Location',
            'price' => 100.00,
            'stock' => 10,
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
        ];

        $response = $this->actingAs($user, 'api')->json('POST', '/api/products', $productData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    public function test_store_gestionnaire_user(): void
    {
        $role = Role::create(['name' => Role::GESTIONNAIRE]);
        $user = User::factory()->create(['role_id' => $role->id]);
        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'reference' => 'TEST123',
            'location' => 'Test Location',
            'price' => 100.00,
            'stock' => 10,
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
        ];

        $response = $this->actingAs($user, 'api')->json('POST', '/api/products', $productData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    public function test_store_user(): void
    {
        $role = Role::create(['name' => Role::CLIENT]);
        $user = User::factory()->create(['role_id' => $role->id]);
        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'reference' => 'TEST123',
            'location' => 'Test Location',
            'price' => 100.00,
            'stock' => 10,
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
        ];

        $response = $this->actingAs($user, 'api')->json('POST', '/api/products', $productData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('products', ['name' => 'Test Product']);
    }

    public function test_update_product_admin_user(): void
    {
        $product = Product::factory()->create();
        $role = Role::create(['name' => Role::ADMIN]);
        $user = User::factory()->create(['role_id' => $role->id]);

        $productData = [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'reference' => 'TEST1234',
            'location' => 'Updated Location',
            'price' => 150.00,
            'stock' => 20,
        ];

        $response = $this->actingAs($user, 'api')->json('PUT', '/api/products/' . $product->id, $productData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', ['name' => 'Updated Product']);
    }

    public function test_update_product_gestionnaire_user(): void
    {
        $product = Product::factory()->create();
        $role = Role::create(['name' => Role::GESTIONNAIRE]);
        $user = User::factory()->create(['role_id' => $role->id]);

        $productData = [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'reference' => 'TEST1234',
            'location' => 'Updated Location',
            'price' => 150.00,
            'stock' => 20,
        ];

        $response = $this->actingAs($user, 'api')->json('PUT', '/api/products/' . $product->id, $productData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', ['name' => 'Updated Product']);
    }

    public function test_update_product_user(): void
    {
        $product = Product::factory()->create();
        $role = Role::create(['name' => Role::CLIENT]);
        $user = User::factory()->create(['role_id' => $role->id]);

        $productData = [
            'name' => 'Updated Product',
            'description' => 'Updated Description',
            'reference' => 'TEST1234',
            'location' => 'Updated Location',
            'price' => 150.00,
            'stock' => 20,
        ];

        $response = $this->actingAs($user, 'api')->json('PUT', '/api/products/' . $product->id, $productData);

        $response->assertStatus(403);
    }

    public function test_delete_product_admin_user(): void
    {
        $product = Product::factory()->create();
        $role = Role::create(['name' => Role::ADMIN]);
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user, 'api')->json('DELETE', '/api/products/' . $product->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_delete_product_gestionnaire_user(): void
    {
        $product = Product::factory()->create();
        $role = Role::create(['name' => Role::GESTIONNAIRE]);
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user, 'api')->json('DELETE', '/api/products/' . $product->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_delete_product_user(): void
    {
        $product = Product::factory()->create();
        $role = Role::create(['name' => Role::CLIENT]);
        $user = User::factory()->create(['role_id' => $role->id]);

        $response = $this->actingAs($user, 'api')->json('DELETE', '/api/products/' . $product->id);

        $response->assertStatus(403);
    }
}
