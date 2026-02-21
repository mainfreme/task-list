<?php

declare(strict_types=1);

namespace App\Crm\Infrastructure\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrmClientContactsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientIds = DB::table('clients')->pluck('id')->toArray();

        if (empty($clientIds)) {
            $this->command->warn('No clients found. Skipping CRM client contacts seeding.');

            return;
        }

        $now = now();

        $contacts = [
            [
                'id' => (string) Str::uuid(),
                'client_uuid' => $clientIds[0],
                'type' => 'email',
                'value' => 'kontakt@softwellhouse.pl',
                'country_prefix' => null,
                'contact_role' => 'sales',
                'is_primary' => true,
                'is_active' => true,
                'is_verified' => true,
                'created_at' => $now->copy()->subDays(25),
                'updated_at' => $now->copy()->subDays(5),
            ],
            [
                'id' => (string) Str::uuid(),
                'client_uuid' => $clientIds[0],
                'type' => 'phone',
                'value' => '221234567',
                'country_prefix' => '+48',
                'contact_role' => 'billing',
                'is_primary' => false,
                'is_active' => true,
                'is_verified' => false,
                'created_at' => $now->copy()->subDays(24),
                'updated_at' => $now->copy()->subDays(6),
            ],
            [
                'id' => (string) Str::uuid(),
                'client_uuid' => $clientIds[1],
                'type' => 'website',
                'value' => 'https://nordic-logistics.example.com',
                'country_prefix' => null,
                'contact_role' => 'admin',
                'is_primary' => true,
                'is_active' => true,
                'is_verified' => true,
                'created_at' => $now->copy()->subDays(16),
                'updated_at' => $now->copy()->subDays(4),
            ],
            [
                'id' => (string) Str::uuid(),
                'client_uuid' => $clientIds[2],
                'type' => 'mobile',
                'value' => '501234567',
                'country_prefix' => '+48',
                'contact_role' => 'other',
                'is_primary' => true,
                'is_active' => true,
                'is_verified' => false,
                'created_at' => $now->copy()->subDays(6),
                'updated_at' => $now->copy()->subDays(1),
            ],
        ];

        DB::table('client_contacts')->insert($contacts);

        $this->command->info('CRM client contacts seeded successfully!');
    }
}
