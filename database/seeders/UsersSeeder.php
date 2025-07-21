<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleIds = Role::pluck('id')->toArray();
        if (app()->environment('local')) {
            $storeIds = Store::factory(20)->create()->pluck('id')->toArray();

            User::factory(50)->create([
                'role_id' => fn() => $roleIds[array_rand($roleIds)],
                'store_id' => function (array $attributes) use ($storeIds) {
                    return $attributes['role_id'] === 4
                        ? $storeIds[array_rand($storeIds)]
                        : null;
                },
            ]);

            User::firstOrCreate(
                ['email' => 'thomas@psl.fr'],
                [
                    'firstname' => 'Thomas',
                    'lastname' => 'Martins',
                    'password' => bcrypt('password'),
                    'role_id' => 1,
                    'phone' => '0606060606',
                    'remember_token' => Str::random(10),
                ]
            );

            User::firstOrCreate(
                ['email' => 'thomas@example.fr'],
                [
                    'firstname' => 'Thomas',
                    'lastname' => 'Martins',
                    'password' => bcrypt('password'),
                    'role_id' => 4,
                    'phone' => '0606060606',
                    'remember_token' => Str::random(10),
                    'store_id' => $storeIds[array_rand($storeIds)],
                ]
            );
        } else {
            User::firstOrCreate(
                ['email' => 'thomas@psl.fr'],
                [
                    'firstname' => 'Thomas',
                    'lastname' => 'Martins',
                    'password' => bcrypt('password'),
                    'role_id' => 1,
                    'phone' => '0606060606',
                    'remember_token' => Str::random(10),
                ]
            );
        }
    }
}
