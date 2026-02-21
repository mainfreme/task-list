<?php

declare(strict_types=1);

namespace App\Crm\Infrastructure\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrmAddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientIds = DB::table('clients')->pluck('id')->toArray();

        if (empty($clientIds)) {
            $this->command->warn('No clients found. Skipping CRM addresses seeding.');

            return;
        }

        $now = now();

        $addresses = [
            [
                'id' => (string) Str::uuid(),
                'client_uuid' => $clientIds[0],
                'street' => 'ul. Chmielna',
                'postal_code' => '00-021',
                'city' => 'Warszawa',
                'state_province' => 'mazowieckie',
                'country' => 'Polska',
                'additional_info' => 'Siedziba glowna, budynek B.',
                'house_number' => '73',
                'apartment_number' => '12',
                'type' => 'registered_office',
                'is_primary' => true,
                'is_active' => true,
                'latitude' => 52.232222,
                'longitude' => 21.017222,
                'added_at' => $now->copy()->subDays(28),
                'created_at' => $now->copy()->subDays(28),
                'updated_at' => $now->copy()->subDays(3),
            ],
            [
                'id' => (string) Str::uuid(),
                'client_uuid' => $clientIds[0],
                'street' => 'ul. Przemyslowa',
                'postal_code' => '30-701',
                'city' => 'Krakow',
                'state_province' => 'malopolskie',
                'country' => 'Polska',
                'additional_info' => 'Magazyn regionalny.',
                'house_number' => '12',
                'apartment_number' => '2',
                'type' => 'shipping',
                'is_primary' => false,
                'is_active' => true,
                'latitude' => 50.040556,
                'longitude' => 19.969167,
                'added_at' => $now->copy()->subDays(20),
                'created_at' => $now->copy()->subDays(20),
                'updated_at' => $now->copy()->subDays(5),
            ],
            [
                'id' => (string) Str::uuid(),
                'client_uuid' => $clientIds[1],
                'street' => 'ul. Portowa',
                'postal_code' => '81-341',
                'city' => 'Gdynia',
                'state_province' => 'pomorskie',
                'country' => 'Polska',
                'additional_info' => 'Biuro operacyjne.',
                'house_number' => '5',
                'apartment_number' => '3',
                'type' => 'billing',
                'is_primary' => true,
                'is_active' => true,
                'latitude' => 54.518889,
                'longitude' => 18.540000,
                'added_at' => $now->copy()->subDays(18),
                'created_at' => $now->copy()->subDays(18),
                'updated_at' => $now->copy()->subDays(4),
            ],
            [
                'id' => (string) Str::uuid(),
                'client_uuid' => $clientIds[2],
                'street' => 'ul. Szeroka',
                'postal_code' => '80-835',
                'city' => 'Gdansk',
                'state_province' => 'pomorskie',
                'country' => 'Polska',
                'additional_info' => 'Adres korespondencyjny.',
                'house_number' => '14',
                'apartment_number' => '7',
                'type' => 'other',
                'is_primary' => true,
                'is_active' => true,
                'latitude' => 54.352000,
                'longitude' => 18.646000,
                'added_at' => $now->copy()->subDays(6),
                'created_at' => $now->copy()->subDays(6),
                'updated_at' => $now->copy()->subDays(1),
            ],
        ];

        DB::table('addresses')->insert($addresses);

        foreach ($addresses as $address) {
            if ($address['is_primary']) {
                DB::table('clients')
                    ->where('id', $address['client_uuid'])
                    ->update([
                        'address_uuid' => $address['id'],
                        'updated_at' => $now,
                    ]);
            }
        }

        $this->command->info('CRM addresses seeded successfully!');
    }
}
