<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;

class PersonalAccessClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Vérifier si un client d'accès personnel existe déjà
        if (! Client::where('personal_access_client', true)->exists()) {
            $clientRepository = app(ClientRepository::class);

            // Créer le client d'accès personnel
            $clientRepository->createPersonalAccessClient(
                null,
                'Personal Access Client',
                config('app.url')
            );

            $this->command->info('Client d\'accès personnel créé avec succès.');
        } else {
            $this->command->info('Un client d\'accès personnel existe déjà.');
        }
    }
}
