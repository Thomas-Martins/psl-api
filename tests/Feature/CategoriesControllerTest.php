<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoriesControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Role $adminRole;
    protected Role $gestionnaireRole;
    protected Role $logisticienRole;
    protected Role $clientRole;


    /**
     * Set up the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Création des rôles
        $this->adminRole = Role::create(['name' => 'admin']);
        $this->gestionnaireRole = Role::create(['name' => 'gestionnaire']);
        $this->logisticienRole = Role::create(['name' => 'logisticien']);
        $this->clientRole = Role::create(['name' => 'client']);
    }

    /**
     * Test the index method of CategoriesController for an admin user.
     * @return void
     */
    public function test_index_categories_admin_user()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $response = $this->actingAs($admin, 'api')->json('GET', '/api/categories');

        $response->assertStatus(200);
    }

    /**
     * Test the index method of CategoriesController for a client user.
     * @return void
     */
    public function test_index_categories_client_user()
    {
        $client = User::factory()->create(['role_id' => $this->clientRole->id]);

        $response = $this->actingAs($client, 'api')->json('GET', '/api/categories');

        $response->assertStatus(200);
    }

    /**
     * Test the store method of CategoriesController for an admin user.
     * @return void
     */
    public function test_store_categories_admin_user()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $data = [
            'name' => 'Test Admin Category',
        ];

        $response = $this->actingAs($admin, 'api')->json('POST', '/api/categories', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', $data);
    }

    /**
     * Test the store method of CategoriesController for a gestionnaire user.
     * @return void
     */
    public function test_store_categories_gestionnaire_user()
    {
        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);

        $data = [
            'name' => 'Test Gestionnaire Category',
        ];

        $response = $this->actingAs($gestionnaire, 'api')->json('POST', '/api/categories', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', $data);
    }

    /**
     * Test the store method of CategoriesController for an unauthorized user.
     * @return void
     */
    public function test_store_categories_unauthorized_user(){
        $logisticien = User::factory()->create(['role_id' => $this->logisticienRole->id]);

        $data = [
            'name' => 'Test Unauthorized Category',
        ];

        $response = $this->actingAs($logisticien, 'api')->json('POST', '/api/categories', $data);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);
    }

    /**
     * Test the show method of CategoriesController for an admin user.
     * @return void
     */
    public function test_show_categories_admin_user()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $category = Category::factory()->create();

        $response = $this->actingAs($admin, 'api')->json('GET', '/api/categories/' . $category->id);

        $response->assertStatus(200);
        $response->assertJson($category->toArray());
    }

    /**
     * Test the show method of CategoriesController for a gestionnaire user.
     * @return void
     */
    public function test_show_categories_gestionnaire_user()
    {
        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);

        $category = Category::factory()->create();

        $response = $this->actingAs($gestionnaire, 'api')->json('GET', '/api/categories/' . $category->id);

        $response->assertStatus(200);
        $response->assertJson($category->toArray());
    }

    /**
     * Test the show method of CategoriesController for a client.
     * @return void
     */
    public function test_show_categories_client_user()
    {
        $client = User::factory()->create(['role_id' => $this->clientRole->id]);

        $category = Category::factory()->create();

        $response = $this->actingAs($client, 'api')->json('GET', '/api/categories/' . $category->id);

        $response->assertStatus(200);
        $response->assertJson($category->toArray());
    }

    /**
     * Test the update method of CategoriesController for an admin user.
     * @return void
     */
    public function test_update_categories_admin_user()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $category = Category::factory()->create();

        $data = [
            'name' => 'Updated Admin Category',
        ];

        $response = $this->actingAs($admin, 'api')->json('PUT', '/api/categories/' . $category->id, $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('categories', array_merge(['id' => $category->id], $data));
    }

    /**
     * Test the update method of CategoriesController for a gestionnaire user.
     * @return void
     */
    public function test_update_categories_gestionnaire_user()
    {
        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);

        $category = Category::factory()->create();

        $data = [
            'name' => 'Updated Gestionnaire Category',
        ];

        $response = $this->actingAs($gestionnaire, 'api')->json('PUT', '/api/categories/' . $category->id, $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('categories', array_merge(['id' => $category->id], $data));
    }

    /**
     * Test the update method of CategoriesController for an unauthorized user.
     * @return void
     */
    public function test_update_categories_unauthorized_user()
    {
        $logisticien = User::factory()->create(['role_id' => $this->logisticienRole->id]);

        $category = Category::factory()->create();

        $data = [
            'name' => 'Updated Unauthorized Category',
        ];

        $response = $this->actingAs($logisticien, 'api')->json('PUT', '/api/categories/' . $category->id, $data);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);
    }

    /**
     * Test the destroy method of CategoriesController for an admin user.
     * @return void
     */
    public function test_destroy_categories_admin_user()
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $category = Category::factory()->create();

        $response = $this->actingAs($admin, 'api')->json('DELETE', '/api/categories/' . $category->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /**
     * Test the destroy method of CategoriesController for a gestionnaire user.
     * @return void
     */
    public function test_destroy_categories_gestionnaire_user()
    {
        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);

        $category = Category::factory()->create();

        $response = $this->actingAs($gestionnaire, 'api')->json('DELETE', '/api/categories/' . $category->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /**
     * Test the destroy method of CategoriesController for an unauthorized user.
     * @return void
     */
    public function test_destroy_categories_unauthorized_user()
    {
        $logisticien = User::factory()->create(['role_id' => $this->logisticienRole->id]);

        $category = Category::factory()->create();

        $response = $this->actingAs($logisticien, 'api')->json('DELETE', '/api/categories/' . $category->id);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);
    }
}
