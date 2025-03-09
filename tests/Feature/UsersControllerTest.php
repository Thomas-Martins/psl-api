<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use http\Params;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Role $adminRole;
    protected Role $userRole;
    protected Role $clientRole;


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
     * Index method should return all users for admin.
     */
    public function test_admin_index_users(): void
    {
        // Create some users
        User::factory()->count(10)->state(new Sequence(
            ['role_id' => $this->adminRole->id],
            ['role_id' => $this->gestionnaireRole->id],
            ['role_id' => $this->userRole->id]
        ))->create();

        // Connect as admin
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $response = $this->actingAs($admin, 'api')
            ->json('GET', '/api/users');

        $response->assertStatus(200)
            ->assertJsonCount(11);
    }

    /**
     * Index method should return 401 for non-admin users.
     */
    public function test_non_admin_index_users(): void
    {
        // Create some users
        User::factory()->count(10)->state(new Sequence(
            ['role_id' => $this->adminRole->id],
            ['role_id' => $this->gestionnaireRole->id],
            ['role_id' => $this->userRole->id]
        ))->create();

        // Connect as non-admin
        $nonAdmin = User::factory()->create(['role_id' => $this->userRole->id]);

        $response = $this->actingAs($nonAdmin, 'api')
            ->json('GET', '/api/users?onlyUsers');

        $response->assertStatus(403);
    }

    public function test_index_clients()
    {
        // Create some users
        // Create some users
        User::factory()->count(10)->state(new Sequence(
            ['role_id' => $this->clientRole->id],
        ))->create();

        // Connect as admin
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $response = $this->actingAs($admin, 'api')
            ->json('GET', '/api/users?onlyCustomers');

        $response->assertStatus(200)
            ->assertJsonCount(10);
    }

    /**
     * Show method should return a user for admin.
     */
    public function test_show_user_admin(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Connect as admin
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $response = $this->actingAs($admin, 'api')
            ->json('GET', "/api/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson($user->toArray());
    }

    /**
     * Show method should return 401 for non-admin users.
     */
    public function test_show_user_non_admin(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Connect as non-admin
        $nonAdmin = User::factory()->create(['role_id' => $this->userRole->id]);

        $response = $this->actingAs($nonAdmin, 'api')
            ->json('GET', "/api/users/{$user->id}");

        $response->assertStatus(403);
    }

    /**
     * Store method should create a user for admin.
     */
    public function test_store_user_admin(): void
    {
        // Connect as admin
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        // Create a user
        $response = $this->actingAs($admin, 'api')
            ->json('POST','/api/users', [
                'lastname' => 'Doe',
                'firstname' => 'John',
                'email' => 'john.doe@psl.fr',
                'password' => 'password',
                'role_id' => $this->userRole->id,
                'phone' => '0123456789',
                'address' => '1 rue de Paris',
                'city' => 'Paris',
                'zipcode' => '75000',
            ]);

        $response->assertStatus(201);
    }

    /**
     * Store method should return 401 for non-admin users.
     */
    public function test_store_user_non_admin(): void
    {
        // Connect as admin
        $user = User::factory()->create(['role_id' => $this->userRole->id]);
        // Create a user
        $response = $this->actingAs($user, 'api')
            ->json('POST','/api/users', [
                'lastname' => 'Doe',
                'firstname' => 'John',
                'email' => 'john.doe@psl.fr',
                'password' => 'password',
                'role_id' => $this->userRole->id,
                'phone' => '0123456789',
                'address' => '1 rue de Paris',
                'city' => 'Paris',
                'zipcode' => '75000',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Update method should update a user for admin.
     */
    public function test_update_user_admin(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Connect as admin
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        // Update the user
        $response = $this->actingAs($admin, 'api')
            ->json('PUT',"/api/users/{$user->id}", [
                'lastname' => 'Doe',
                'firstname' => 'John',
                'email' => $user->email,
                'password' => 'password',
                'role_id' => $this->userRole->id,
                'phone' => '0123456789',
                'address' => '1 rue de Paris',
                'city' => 'Paris',
                'zipcode' => '75000',
            ]);

        $response->assertStatus(200);
    }

    /**
     * Update method should return 401 for non-admin users.
     */
    public function test_update_user_non_admin(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Connect as non-admin
        $nonAdmin = User::factory()->create(['role_id' => $this->userRole->id]);

        // Update the user
        $response = $this->actingAs($nonAdmin, 'api')
            ->json('PUT',"/api/users/{$user->id}", [
                'lastname' => 'Doe',
                'firstname' => 'John',
                'email' => $user->email,
                'password' => 'password',
                'role_id' => $this->userRole->id,
                'phone' => '0123456789',
                'address' => '1 rue de Paris',
                'city' => 'Paris',
                'zipcode' => '75000',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Destroy method should delete a user for admin.
     */
    public function test_destroy_user_admin(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Connect as admin
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        // Delete the user
        $response = $this->actingAs($admin, 'api')
            ->json('DELETE',"/api/users/{$user->id}");

        $response->assertStatus(204);
    }

    /**
     * Destroy method should return 401 for non-admin users.
     */
    public function test_destroy_user_non_admin(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Connect as non-admin
        $nonAdmin = User::factory()->create(['role_id' => $this->userRole->id]);

        // Delete the user
        $response = $this->actingAs($nonAdmin, 'api')
            ->json('DELETE',"/api/users/{$user->id}");

        $response->assertStatus(403);
    }

}
