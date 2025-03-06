<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesSeeder::class);
        $this->call(PersonalAccessClientSeeder::class);

        // Récupérer tous les IDs des rôles existants
        $roleIds = Role::pluck('id')->toArray();

        // Créer 10 utilisateurs avec un role_id aléatoire
        User::factory(50)->create([
            'role_id' => fn() => $roleIds[array_rand($roleIds)],
        ]);

        User::factory()->create([
            'firstname' => 'Thomas',
            'lastname' => 'Martins',
            'email' => 'test@psl.fr',
            'password' => bcrypt('password'),
            'role_id' => 1,
            'phone' => '0606060606',
            'address' => '1 rue de la paix',
            'city' => 'Paris',
            'zipcode' => '75000',
            'remember_token' => Str::random(10),
        ]);
    }
}
