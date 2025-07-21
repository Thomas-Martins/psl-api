<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Role $adminRole;
    protected Role $userRole;
    protected Role $clientRole;
    protected Role $gestionnaireRole;


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
        /** @var User $admin */
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
        /** @var User $nonAdmin */
        $nonAdmin = User::factory()->create(['role_id' => $this->userRole->id]);

        $response = $this->actingAs($nonAdmin, 'api')
            ->json('GET', '/api/users?onlyUsers');

        $response->assertStatus(403);
    }

    /**
     * Index method should return paginated results for clients.
     */
    public function test_index_clients(): void
    {
        // Create some users
        User::factory()->count(10)->state(new Sequence(
            ['role_id' => $this->clientRole->id],
        ))->create();

        // Connect as admin
        /** @var User $admin */
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
        /** @var User $user */
        $user = User::factory()->create();

        // Connect as admin
        /** @var User $admin */
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $response = $this->actingAs($admin, 'api')
            ->json('GET', "/api/users/{$user->id}");

        $response->assertStatus(200);

        // Vérifier que la structure de la réponse contient les données de l'utilisateur
        $responseData = $response->json('data');
        $this->assertEquals($user->id, $responseData['id']);
        $this->assertEquals($user->firstname, $responseData['firstname']);
        $this->assertEquals($user->lastname, $responseData['lastname']);
        $this->assertEquals($user->email, $responseData['email']);
    }

    /**
     * Show method should return 401 for non-admin users.
     */
    public function test_show_user_non_admin(): void
    {
        // Create a user
        /** @var User $user */
        $user = User::factory()->create();

        // Connect as non-admin
        /** @var User $nonAdmin */
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
        /** @var User $admin */
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        // Create a user
        $response = $this->actingAs($admin, 'api')
            ->json('POST', '/api/users', [
                'lastname' => 'Doe',
                'firstname' => 'John',
                'email' => 'john.doe@psl.fr',
                'role_id' => $this->userRole->id,
                'phone' => '0123456789',
                'locale' => 'en',
            ]);

        $response->assertStatus(201);
    }

    /**
     * Store method should return 401 for non-admin users.
     */
    public function test_store_user_non_admin(): void
    {
        // Connect as admin
        /** @var User $user */
        $user = User::factory()->create(['role_id' => $this->userRole->id]);
        // Create a user
        $response = $this->actingAs($user, 'api')
            ->json('POST', '/api/users', [
                'lastname' => 'Doe',
                'firstname' => 'John',
                'email' => 'john.doe@psl.fr',
                'password' => 'password',
                'role_id' => $this->userRole->id,
                'phone' => '0123456789',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Update method should update a user for admin.
     */
    public function test_update_user_admin(): void
    {
        // Create a user
        /** @var User $user */
        $user = User::factory()->create();

        // Connect as admin
        /** @var User $admin */
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        // Update the user
        $response = $this->actingAs($admin, 'api')
            ->json('PUT', "/api/users/{$user->id}", [
                'lastname' => 'Doe',
                'firstname' => 'John',
                'email' => $user->email,
                'password' => 'password',
                'role_id' => $this->userRole->id,
                'phone' => '0123456789',
            ]);

        $response->assertStatus(200);
    }

    /**
     * Update method should return 401 for non-admin users.
     */
    public function test_update_user_non_admin(): void
    {
        // Create a user
        /** @var User $user */
        $user = User::factory()->create();

        // Connect as non-admin
        /** @var User $nonAdmin */
        $nonAdmin = User::factory()->create(['role_id' => $this->userRole->id]);

        // Update the user
        $response = $this->actingAs($nonAdmin, 'api')
            ->json('PUT', "/api/users/{$user->id}", [
                'lastname' => 'Doe',
                'firstname' => 'John',
                'email' => $user->email,
                'password' => 'password',
                'role_id' => $this->userRole->id,
                'phone' => '0123456789',
            ]);

        $response->assertStatus(403);
    }

    /**
     * Destroy method should delete a user for admin.
     */
    public function test_destroy_user_admin(): void
    {
        // Create a user
        /** @var User $user */
        $user = User::factory()->create();

        // Connect as admin
        /** @var User $admin */
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        // Delete the user
        $response = $this->actingAs($admin, 'api')
            ->json('DELETE', "/api/users/{$user->id}");

        $response->assertStatus(204);
    }

    /**
     * Destroy method should return 401 for non-admin users.
     */
    public function test_destroy_user_non_admin(): void
    {
        // Create a user
        /** @var User $user */
        $user = User::factory()->create();

        // Connect as non-admin
        /** @var User $nonAdmin */
        $nonAdmin = User::factory()->create(['role_id' => $this->userRole->id]);

        // Delete the user
        $response = $this->actingAs($nonAdmin, 'api')
            ->json('DELETE', "/api/users/{$user->id}");

        $response->assertStatus(403);
    }
}
