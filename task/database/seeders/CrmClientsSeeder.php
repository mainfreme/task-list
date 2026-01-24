<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrmClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $clients = [
            [
                'id' => (string) Str::uuid(),
                'address_uuid' => null,
                'name' => 'Softwell House Sp. z o.o.',
                'nip' => '5252674798',
                'regon' => '142345678',
                'pesel' => null,
                'country' => 'Polska',
                'status' => 'active',
                'source' => 'referral',
                'rating' => 5,
                'last_contacted_at' => $now->copy()->subDays(5),
                'next_contact_at' => $now->copy()->addDays(10),
                'notes' => 'Kluczowy klient z duzym potencjalem rozwoju.',
                'is_delete' => false,
                'is_company' => true,
                'created_at' => $now->copy()->subDays(30),
                'updated_at' => $now->copy()->subDays(2),
            ],
            [
                'id' => (string) Str::uuid(),
                'address_uuid' => null,
                'name' => 'Nordic Logistics S.A.',
                'nip' => '9571054231',
                'regon' => '221234567',
                'pesel' => null,
                'country' => 'Polska',
                'status' => 'prospect',
                'source' => 'marketing',
                'rating' => 4,
                'last_contacted_at' => $now->copy()->subDays(12),
                'next_contact_at' => $now->copy()->addDays(5),
                'notes' => 'Wysokie zainteresowanie oferta wdrozeniowa.',
                'is_delete' => false,
                'is_company' => true,
                'created_at' => $now->copy()->subDays(25),
                'updated_at' => $now->copy()->subDays(6),
            ],
            [
                'id' => (string) Str::uuid(),
                'address_uuid' => null,
                'name' => 'Jan Malinowski',
                'nip' => '6781012345',
                'regon' => null,
                'pesel' => '82031412345',
                'country' => 'Polska',
                'status' => 'lead',
                'source' => 'website',
                'rating' => 3,
                'last_contacted_at' => $now->copy()->subDays(2),
                'next_contact_at' => $now->copy()->addDays(14),
                'notes' => 'Lead z formularza kontaktowego.',
                'is_delete' => false,
                'is_company' => false,
                'created_at' => $now->copy()->subDays(7),
                'updated_at' => $now->copy()->subDays(1),
            ],
        ];

        DB::table('clients')->insert($clients);

        $this->command->info('CRM clients seeded successfully!');
    }
}
