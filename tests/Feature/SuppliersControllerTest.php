<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuppliersControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Role $adminRole;
    protected Role $gestionnaireRole;
    protected Role $logisticienRole;
    protected Role $clientRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Création des rôles
        $this->adminRole        = Role::create(['name' => 'admin']);
        $this->gestionnaireRole = Role::create(['name' => 'gestionnaire']);
        $this->logisticienRole  = Role::create(['name' => 'logisticien']);
        $this->clientRole       = Role::create(['name' => 'client']);
    }

    /**
     * Test index des suppliers en tant qu'admin. Doit retourner 200.
     */
    public function test_admin_index_suppliers(): void
    {
        Supplier::factory()->count(5)->create();

        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $response = $this->actingAs($admin, 'api')
            ->json('GET', '/api/suppliers');

        $response->assertStatus(200);
        $this->assertCount(5, $response->json());
    }

    /**
     * Test index des suppliers en tant que gestionnaire. Doit retourner 200.
     */
    public function test_gestionnaire_index_suppliers(): void
    {
        Supplier::factory()->count(5)->create();

        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);

        $response = $this->actingAs($gestionnaire, 'api')
            ->json('GET', '/api/suppliers');

        $response->assertStatus(200);
        $this->assertCount(5, $response->json());
    }

    /**
     * Test index des suppliers en tant que rôle non autorisé (ici logisticien). Doit retourner 403.
     */
    public function test_index_suppliers_as_unauthorized_role(): void
    {
        Supplier::factory()->count(5)->create();

        $logisticien = User::factory()->create(['role_id' => $this->logisticienRole->id]);

        $response = $this->actingAs($logisticien, 'api')
            ->json('GET', '/api/suppliers');

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);
    }

    /**
     * Test création d'un supplier en tant qu'admin. Doit retourner 201.
     */
    public function test_admin_store_supplier(): void
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $data = [
            'name'                      => 'ACME Inc.',
            'email'                     => 'acme@example.com',
            'phone'                     => '0123456789',
            'address'                   => '123 Main Street',
            'zipcode'                   => '75000',
            'city'                      => 'Paris',
            'country'                   => 'France',
            'contact_person_firstname'  => 'John',
            'contact_person_lastname'   => 'Doe',
            'contact_person_email'      => 'john.doe@acme.com',
            'contact_person_phone'      => '0987654321'
        ];

        $response = $this->actingAs($admin, 'api')
            ->json('POST', '/api/suppliers', $data);

        $response->assertStatus(201);
        $response->assertJson(['message' => 'Supplier created']);

        $this->assertDatabaseHas('suppliers', [
            'name'  => 'ACME Inc.',
            'email' => 'acme@example.com',
        ]);
    }

    /**
     * Test création d'un supplier en tant que gestionnaire. Doit retourner 201.
     */
    public function test_gestionnaire_store_supplier(): void
    {
        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);

        $data = [
            'name'                      => 'Best Supplies',
            'email'                     => 'best@example.com',
            'phone'                     => '0123456789',
            'address'                   => '1 Avenue du Test',
            'zipcode'                   => '69000',
            'city'                      => 'Lyon',
            'country'                   => 'France',
            'contact_person_firstname'  => 'Jane',
            'contact_person_lastname'   => 'Smith',
            'contact_person_email'      => 'jane.smith@best.com',
            'contact_person_phone'      => '0987654321'
        ];

        $response = $this->actingAs($gestionnaire, 'api')
            ->json('POST', '/api/suppliers', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('suppliers', ['name' => 'Best Supplies']);
    }

    /**
     * Test création d'un supplier en tant que rôle non autorisé (ici client). Doit retourner 403.
     */
    public function test_store_supplier_as_unauthorized_role(): void
    {
        $client = User::factory()->create(['role_id' => $this->clientRole->id]);

        $data = Supplier::factory()->make()->toArray();

        $response = $this->actingAs($client, 'api')
            ->json('POST', '/api/suppliers', $data);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);
    }

    /**
     * Test affichage d'un supplier (show) en tant qu'admin. Doit retourner 200.
     */
    public function test_admin_show_supplier(): void
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($admin, 'api')
            ->json('GET', "/api/suppliers/{$supplier->id}");

        $response->assertStatus(200);
        $response->assertJson($supplier->toArray());
    }

    /**
     * Test affichage d'un supplier (show) en tant que gestionnaire. Doit retourner 200.
     */
    public function test_gestionnaire_show_supplier(): void
    {
        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($gestionnaire, 'api')
            ->json('GET', "/api/suppliers/{$supplier->id}");

        $response->assertStatus(200);
        $response->assertJson($supplier->toArray());
    }

    /**
     * Test affichage d'un supplier (show) en tant que rôle non autorisé (ici logisticien). Doit retourner 403.
     */
    public function test_users_show_supplier(): void
    {
        $logisticien = User::factory()->create(['role_id' => $this->logisticienRole->id]);
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($logisticien, 'api')
            ->json('GET', "/api/suppliers/{$supplier->id}");

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);
    }

    /**
     * Test mise à jour (update) d'un supplier en tant qu'admin. Doit retourner 200.
     */
    public function test_admin_update_supplier(): void
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $supplier = Supplier::factory()->create();

        $updateData = [
            'name'  => 'Supplier Updated',
            'email' => 'updated@supplier.com'
        ];

        $response = $this->actingAs($admin, 'api')
            ->json('PUT', "/api/suppliers/{$supplier->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Supplier updated']);

        $this->assertDatabaseHas('suppliers', [
            'id'    => $supplier->id,
            'name'  => 'Supplier Updated',
            'email' => 'updated@supplier.com'
        ]);
    }

    /**
     * Test mise à jour (update) d'un supplier en tant que gestionnaire. Doit retourner 200.
     */
    public function test_gestionnaire_update_supplier(): void
    {
        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);
        $supplier = Supplier::factory()->create();

        $updateData = [
            'name'  => 'Gestionnaire Updated',
            'email' => 'gestionnaire@supplier.com'
        ];

        $response = $this->actingAs($gestionnaire, 'api')
            ->json('PUT', "/api/suppliers/{$supplier->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Supplier updated']);

        $this->assertDatabaseHas('suppliers', [
            'id'    => $supplier->id,
            'name'  => 'Gestionnaire Updated',
            'email' => 'gestionnaire@supplier.com'
        ]);
    }

    /**
     * Test mise à jour (update) d'un supplier en tant que rôle non autorisé (ici logisticien). Doit retourner 403.
     */
    public function test_users_update_supplier(): void
    {
        $logisticien = User::factory()->create(['role_id' => $this->logisticienRole->id]);
        $supplier = Supplier::factory()->create();

        $updateData = [
            'name'  => 'NonAuthorized Updated',
            'email' => 'nonauthorized@supplier.com'
        ];

        $response = $this->actingAs($logisticien, 'api')
            ->json('PUT', "/api/suppliers/{$supplier->id}", $updateData);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);

        // Vérifie que la base de données n’a pas été modifiée
        $this->assertDatabaseHas('suppliers', [
            'id'    => $supplier->id,
            'name'  => $supplier->name,  // inchangé
            'email' => $supplier->email, // inchangé
        ]);
    }

    /**
     * Test suppression (destroy) d'un supplier en tant qu'admin. Doit retourner 204.
     */
    public function test_admin_destroy_supplier(): void
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($admin, 'api')
            ->json('DELETE', "/api/suppliers/{$supplier->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    /**
     * Test suppression (destroy) d'un supplier en tant que gestionnaire. Doit retourner 204.
     */
    public function test_gestionnaire_destroy_supplier(): void
    {
        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($gestionnaire, 'api')
            ->json('DELETE', "/api/suppliers/{$supplier->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    /**
     * Test suppression (destroy) d'un supplier en tant que rôle non autorisé (ici logisticien). Doit retourner 403.
     */
    public function test_destroy_supplier_as_unauthorized_role(): void
    {
        $logisticien = User::factory()->create(['role_id' => $this->logisticienRole->id]);
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($logisticien, 'api')
            ->json('DELETE', "/api/suppliers/{$supplier->id}");

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);

        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id]);
    }
}
