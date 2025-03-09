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

        // Création des rôles (similaire à UsersControllerTest)
        $this->adminRole = Role::create(['name' => 'admin']);
        $this->gestionnaireRole = Role::create(['name' => 'gestionnaire']);
        $this->logisticienRole = Role::create(['name' => 'logisticien']);
        $this->clientRole = Role::create(['name' => 'client']);
    }

    /* -------------------------------------------------------------------------
       INDEX
    ------------------------------------------------------------------------- */
    /**
     * Test l'index des fournisseurs (index) en tant qu'admin : doit retourner 200.
     */
    public function test_index_suppliers_as_admin(): void
    {
        Supplier::factory()->count(5)->create();

        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);

        $response = $this->actingAs($admin, 'api')
            ->json('GET', '/api/suppliers');

        $response->assertStatus(200);
        $this->assertCount(5, $response->json());
    }

    /**
     * Test l'index des fournisseurs (index) en tant que gestionnaire : doit retourner 200.
     */
    public function test_index_suppliers_as_gestionnaire(): void
    {
        Supplier::factory()->count(5)->create();

        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);

        $response = $this->actingAs($gestionnaire, 'api')
            ->json('GET', '/api/suppliers');

        $response->assertStatus(200);
        $this->assertCount(5, $response->json());
    }

    /**
     * Test l'index des fournisseurs (index) en tant que rôle non autorisé : doit retourner 405.
     */
    public function test_index_suppliers_as_unauthorized_role(): void
    {
        Supplier::factory()->count(5)->create();

        // Par exemple, un logisticien
        $logisticien = User::factory()->create(['role_id' => $this->logisticienRole->id]);

        $response = $this->actingAs($logisticien, 'api')
            ->json('GET', '/api/suppliers');

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);
    }

    /**
     * Test la création d'un fournisseur (store) en tant qu'admin : doit retourner 201.
     */
    public function test_store_supplier_as_admin(): void
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

        // Vérifie en base de données
        $this->assertDatabaseHas('suppliers', [
            'name'  => 'ACME Inc.',
            'email' => 'acme@example.com',
        ]);
    }

    /**
     * Test la création d'un fournisseur (store) en tant que gestionnaire : doit retourner 201.
     */
    public function test_store_supplier_as_gestionnaire(): void
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
     * Test la création d'un fournisseur (store) en tant que rôle non autorisé : doit retourner 405.
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
     * Test la suppression d'un fournisseur (destroy) en tant qu'admin : doit retourner 204.
     */
    public function test_destroy_supplier_as_admin(): void
    {
        $admin = User::factory()->create(['role_id' => $this->adminRole->id]);
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($admin, 'api')
            ->json('DELETE', "/api/suppliers/{$supplier->id}");

        $response->assertStatus(204);
        // Vérifie que le supplier n'est plus en base
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    /**
     * Test la suppression d'un fournisseur (destroy) en tant que gestionnaire : doit retourner 204.
     */
    public function test_destroy_supplier_as_gestionnaire(): void
    {
        $gestionnaire = User::factory()->create(['role_id' => $this->gestionnaireRole->id]);
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($gestionnaire, 'api')
            ->json('DELETE', "/api/suppliers/{$supplier->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    /**
     * Test la suppression d'un fournisseur (destroy) en tant que rôle non autorisé : doit retourner 405.
     */
    public function test_destroy_supplier_as_unauthorized_role(): void
    {
        $logisticien = User::factory()->create(['role_id' => $this->logisticienRole->id]);
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($logisticien, 'api')
            ->json('DELETE', "/api/suppliers/{$supplier->id}");

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Forbidden']);

        // L'enregistrement est toujours présent
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id]);
    }
}
