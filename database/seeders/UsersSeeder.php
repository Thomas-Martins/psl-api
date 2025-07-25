<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleAdmin = Role::where('name', Role::ADMIN)->first();
        $roleClient = Role::where('name', Role::CLIENT)->first();
        $roleIds = Role::pluck('id')->toArray();

        // Récupère les stores existants
        $storeIds = Store::pluck('id')->toArray();
        if (empty($storeIds)) {
            $this->command->warn('No stores found in database. Client users will be created without store association.');
        }

        // 5 clients liés à un store aléatoire
        User::factory(5)->create([
            'role_id' => $roleClient ? $roleClient->id : 4,
            'store_id' => function () use ($storeIds) {
                return !empty($storeIds) ? $storeIds[array_rand($storeIds)] : null;
            },
        ]);
        // 5 admins sans store
        User::factory(5)->create([
            'role_id' => $roleAdmin ? $roleAdmin->id : 1,
            'store_id' => null,
        ]);

        // Comptes spécifiques
        User::firstOrCreate(
            ['email' => 'thomas@psl.fr'],
            [
                'firstname' => 'Thomas',
                'lastname' => 'Martins',
                'password' => bcrypt('password'),
                'role_id' => $roleAdmin ? $roleAdmin->id : 1,
                'phone' => '0606060606',
                'remember_token' => Str::random(10),
            ]
        );
        User::firstOrCreate(
            ['email' => 'gilles@psl.fr'],
            [
                'firstname' => 'Gilles',
                'lastname' => 'PSL',
                'password' => bcrypt('password'),
                'role_id' => $roleAdmin ? $roleAdmin->id : 1,
                'phone' => '0606060607',
                'remember_token' => Str::random(10),
            ]
        );
        User::firstOrCreate(
            ['email' => 'gilles@test.fr'],
            [
                'firstname' => 'Gilles',
                'lastname' => 'Client',
                'password' => bcrypt('password'),
                'role_id' => $roleClient ? $roleClient->id : 4,
                'phone' => '0606060608',
                'remember_token' => Str::random(10),
                'store_id' => $storeIds ? $storeIds[array_rand($storeIds)] : null,
            ]
        );
    }
}
