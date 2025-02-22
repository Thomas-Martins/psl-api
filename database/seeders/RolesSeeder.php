<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [Role::ADMIN, Role::GESTIONNAIRE, Role::LOGISTICIEN, Role::CLIENT];

        foreach ($roles as $roleName) {
            try {
                Role::firstOrCreate(
                    ['name' => $roleName]
                );
            } catch (\Exception $e) {
                $this->command->error("Failed to create role {$roleName}: " . $e->getMessage());
            }
        }
    }
}
