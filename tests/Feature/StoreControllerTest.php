<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Role $adminRole;
    protected Role $gestionnaireRole;
    protected Role $logisticienRole;
    protected Role $clientRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create(['name' => 'admin']);
        $this->gestionnaireRole = Role::create(['name' => 'gestionnaire']);
        $this->logisticienRole = Role::create(['name' => 'logisticien']);
        $this->clientRole = Role::create(['name' => 'client']);
    }

    /** INDEX */

    public function test_admin_can_list_stores(): void
    {
        Store::factory()->count(5)->create();
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $response = $this->actingAs($admin, 'api')->getJson('/api/stores');

        $response->assertStatus(200);
        $this->assertCount(5, $response->json());
    }

    public function test_gestionnaire_can_list_stores(): void
    {
        Store::factory()->count(3)->create();
        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);

        $response = $this->actingAs($gestionnaire, 'api')->getJson('/api/stores');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json());
    }

    public function test_user_with_unauthorized_role_cannot_list_stores(): void
    {
        Store::factory()->count(2)->create();
        $logisticien = User::factory()->create(['role_id' => $this->logisticienRole->id]);

        $response = $this->actingAs($logisticien, 'api')->getJson('/api/stores');

        $response->assertStatus(403)->assertJson(['message' => 'Forbidden']);
    }

    /** STORE */

    public function test_admin_can_create_store(): void
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $data = Store::factory()->make()->toArray();

        $response = $this->actingAs($admin, 'api')->postJson('/api/stores', $data);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Store created']);

        $this->assertDatabaseHas('stores', ['name' => $data['name']]);
    }

    public function test_gestionnaire_can_create_store(): void
    {
        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);

        $data = Store::factory()->make()->toArray();

        $response = $this->actingAs($gestionnaire, 'api')->postJson('/api/stores', $data);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Store created']);

        $this->assertDatabaseHas('stores', ['name' => $data['name']]);
    }

    public function test_unauthorized_user_cannot_create_store(): void
    {
        $user = User::factory()->create(['role_id' => $this->clientRole->id]);
        $data = Store::factory()->make()->toArray();

        $response = $this->actingAs($user, 'api')->postJson('/api/stores', $data);

        $response->assertStatus(403)->assertJson(['message' => 'Forbidden']);
    }

    /** SHOW */

    public function test_admin_can_show_store(): void
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $store = Store::factory()->create();

        $response = $this->actingAs($admin, 'api')->getJson("/api/stores/{$store->id}");

        $response->assertStatus(200)->assertJson($store->toArray());
    }

    public function test_gestionnaire_can_show_store(): void
    {
        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);
        $store = Store::factory()->create();

        $response = $this->actingAs($gestionnaire, 'api')->getJson("/api/stores/{$store->id}");

        $response->assertStatus(200)->assertJson($store->toArray());
    }

    public function test_unauthorized_user_cannot_show_store(): void
    {
        $user = User::factory()->create(['role_id' => $this->logisticienRole->id]);
        $store = Store::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson("/api/stores/{$store->id}");

        $response->assertStatus(403)->assertJson(['message' => 'Forbidden']);
    }

    /** UPDATE */

    public function test_admin_can_update_store(): void
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $store = Store::factory()->create();

        $update = ['name' => 'Updated Store'];

        $response = $this->actingAs($admin, 'api')->putJson("/api/stores/{$store->id}", $update);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Store updated']);

        $this->assertDatabaseHas('stores', ['name' => 'Updated Store']);
    }

    public function test_gestionnaire_can_update_store(): void
    {
        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);
        $store = Store::factory()->create();

        $update = ['name' => 'Gestionnaire Update'];

        $response = $this->actingAs($gestionnaire, 'api')->putJson("/api/stores/{$store->id}", $update);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Store updated']);

        $this->assertDatabaseHas('stores', ['name' => 'Gestionnaire Update']);
    }

    public function test_unauthorized_user_cannot_update_store(): void
    {
        $user = User::factory()->create(['role_id' => $this->logisticienRole->id]);
        $store = Store::factory()->create();

        $update = ['name' => 'Forbidden Update'];

        $response = $this->actingAs($user, 'api')->putJson("/api/stores/{$store->id}", $update);

        $response->assertStatus(403)->assertJson(['message' => 'Forbidden']);
    }

    /** DESTROY */

    public function test_admin_can_delete_store(): void
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $store = Store::factory()->create();

        $response = $this->actingAs($admin, 'api')->deleteJson("/api/stores/{$store->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('stores', ['id' => $store->id]);
    }

    public function test_gestionnaire_can_delete_store(): void
    {
        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);
        $store = Store::factory()->create();

        $response = $this->actingAs($gestionnaire, 'api')->deleteJson("/api/stores/{$store->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('stores', ['id' => $store->id]);
    }

    public function test_unauthorized_user_cannot_delete_store(): void
    {
        $user = User::factory()->create(['role_id' => $this->clientRole->id]);
        $store = Store::factory()->create();

        $response = $this->actingAs($user, 'api')->deleteJson("/api/stores/{$store->id}");

        $response->assertStatus(403)->assertJson(['message' => 'Forbidden']);
    }
}
