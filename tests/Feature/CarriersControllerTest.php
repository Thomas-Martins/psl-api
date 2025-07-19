<?php

namespace Tests\Feature;

use App\Models\Carrier;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarriersControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Role $adminRole;
    protected Role $gestionnaireRole;
    protected Role $logisticienRole;
    protected Role $clientRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Création des rôles (similaire à UsersControllerTest)
        $this->adminRole = Role::create(['name' => 'admin']);
        $this->gestionnaireRole = Role::create(['name' => 'gestionnaire']);
        $this->logisticienRole = Role::create(['name' => 'logisticien']);
        $this->clientRole = Role::create(['name' => 'client']);
    }

    /**
     * Test Index of carriers when the user is admin. Should return 200
     */
    public function test_admin_index_carriers(): void
    {
        Carrier::factory()->count(5)->create();

        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $response = $this->actingAs($admin, 'api')->json('GET', '/api/carriers');

        $response->assertStatus(200);
        $this->assertCount(5, $response->json());
    }

    /**
     * Test Index of carriers when the user is gestionnaire. Should return 200
     */
    public function test_gestionnaire_index_carriers(): void
    {
        Carrier::factory()->count(5)->create();

        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);

        $response = $this->actingAs($gestionnaire, 'api')
            ->json('GET', '/api/carriers');

        $response->assertStatus(200);
        $this->assertCount(5, $response->json());
    }

    /**
     * Test Index of carriers when the user is unauthorized. Should return 403
     */
    public function test_index_carriers_as_unauthorized_role(): void
    {
        Carrier::factory()->count(5)->create();

        $logisticien = User::factory()->create(['role_id' => $this->logisticienRole->id]);

        $response = $this->actingAs($logisticien, 'api')
            ->json('GET', '/api/carriers');

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);
    }

    /**
     * Test store a carrier when the user is admin. Should return 200.
     */
    public function test_admin_store_carrier(): void
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $data = [
            'name'                      => 'Transporteur',
            'email'                     => 'contact@transporteur.com',
            'phone'                     => '0123456789',
            'address'                   => '123 Main Street',
            'zipcode'                   => '75000',
            'city'                      => 'Paris',
            'contact_person_firstname'  => 'John',
            'contact_person_lastname'   => 'Doe',
            'contact_person_email'      => 'john.doe@transporteur.com',
            'contact_person_phone'      => '0987654321'
        ];

        $response = $this->actingAs($admin, 'api')
            ->json('POST', '/api/carriers', $data);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Carrier created']);

        $this->assertDatabaseHas('carriers', [
            'name'  => 'Transporteur',
            'email' => 'contact@transporteur.com',
        ]);
    }

    public function test_gestionnaire_store_carrier(): void
    {
        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);

        $data = [
            'name'                      => 'Transporteur 2',
            'email'                     => 'contact@transporteur2.com',
            'phone'                     => '0123456789',
            'address'                   => '123 Main Street',
            'zipcode'                   => '75000',
            'city'                      => 'Paris',
            'contact_person_firstname'  => 'John',
            'contact_person_lastname'   => 'Doe',
            'contact_person_email'      => 'john.doe@transporteur2.com',
            'contact_person_phone'      => '0987654321'
        ];

        $response = $this->actingAs($gestionnaire, 'api')
            ->json('POST', '/api/carriers', $data);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Carrier created']);

        $this->assertDatabaseHas('carriers', [
            'name'  => 'Transporteur 2',
            'email' => 'contact@transporteur2.com',
        ]);
    }

    public function test_users_store_carrier(): void
    {
        $user = User::factory()->create(['role_id' => $this->logisticienRole->id]);

        $data = Carrier::factory()->make()->toArray();

        $response = $this->actingAs($user, 'api')
            ->json('POST', '/api/carriers', $data);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);
    }

    public function test_admin_show_carrier(): void
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $data = Carrier::factory()->create();

        $response = $this->actingAs($admin, 'api')
            ->json('GET', '/api/carriers/' . $data->id);

        $response->assertStatus(200);

        $response->assertJson($data->toArray());
    }

    public function test_gestionnaire_show_carrier(): void
    {
        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);

        $data = Carrier::factory()->create();

        $response = $this->actingAs($gestionnaire, 'api')
            ->json('GET', '/api/carriers/' . $data->id);

        $response->assertStatus(200);

        $response->assertJson($data->toArray());
    }

    public function test_users_show_carrier(): void
    {
        $user = User::factory()->create(['role_id' => $this->logisticienRole->id]);

        $data = Carrier::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->json('GET', '/api/carriers/' . $data->id);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);
    }

    public function test_admin_update_carrier(): void
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $data = Carrier::factory()->create();

        $update = [
            'name' => 'Update Name',
            'email' => 'emailupdated@email.com'
        ];

        $response = $this->actingAs($admin, 'api')
            ->json('PUT', '/api/carriers/' . $data->id, $update);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Carrier updated']);
        $this->assertDatabaseHas('carriers', ['name' => 'Update Name']);
    }

    public function test_gestionnaire_update_carrier(): void
    {
        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);

        $data = Carrier::factory()->create();

        $update = [
            'name' => 'Test Update',
            'email' => 'email+2@email.com'
        ];

        $response = $this->actingAs($gestionnaire, 'api')
            ->json('PUT', '/api/carriers/' . $data->id, $update);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Carrier updated']);
        $this->assertDatabaseHas('carriers', ['name' => 'Test Update']);
    }

    public function test_users_update_carrier(): void
    {
        $users = User::factory()->create(['role_id' => $this->logisticienRole->id]);

        $data = Carrier::factory()->create();

        $update = [
            'name' => 'Test Update',
            'email' => 'email+2@email.com'
        ];

        $response = $this->actingAs($users, 'api')
            ->json('PUT', '/api/carriers/' . $data->id, $update);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);
    }

    public function test_admin_destroy_carrier(): void
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $data = Carrier::factory()->create();

        $response = $this->actingAs($admin, 'api')
            ->json('DELETE', '/api/carriers/' . $data->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('carriers', ['id' => $data->id]);
    }

    public function test_gestionnaire_destroy_carrier(): void
    {
        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);

        $data = Carrier::factory()->create();

        $response = $this->actingAs($gestionnaire, 'api')
            ->json('DELETE', '/api/carriers/' . $data->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('carriers', ['id' => $data->id]);
    }

    public function test_users_destroy_carrier(): void
    {
        $users = User::factory()->create(['role_id' => $this->logisticienRole->id]);

        $data = Carrier::factory()->create();

        $response = $this->actingAs($users, 'api')
            ->json('DELETE', '/api/carriers/' . $data->id);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);
    }

}
