<?php

declare(strict_types=1);

namespace App\Crm\Infrastructure\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrmCompanyAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientIds = DB::table('clients')->pluck('id')->toArray();

        if (empty($clientIds)) {
            $this->command->warn('No clients found. Skipping CRM company accounts seeding.');
            return;
        }

        $addressByClient = DB::table('addresses')
            ->select('id', 'client_uuid')
            ->get()
            ->groupBy('client_uuid');

        $now = now();

        $getAddressId = fn (string $clientId): ?string => $addressByClient->get($clientId)?->first()?->id;

        $accounts = [
            [
                'id' => (string) Str::uuid(),
                'address_uuid' => $getAddressId($clientIds[0]),
                'client_uuid' => $clientIds[0],
                'name' => 'Bank Pekao',
                'number' => '10203040506070809000000001',
                'swift_code' => 'PKOPPLPW',
                'iban' => 'PL101020304050607080900000001',
                'bic' => 'PKOPPLPW',
                'account_name' => 'Softwell House Operations',
                'is_active' => true,
                'is_primary' => true,
                'created_at' => $now->copy()->subDays(21),
                'updated_at' => $now->copy()->subDays(3),
            ],
            [
                'id' => (string) Str::uuid(),
                'address_uuid' => $getAddressId($clientIds[1]),
                'client_uuid' => $clientIds[1],
                'name' => 'mBank',
                'number' => '11402005580000012345678901',
                'swift_code' => 'BREXPLPW',
                'iban' => 'PL11402005580000012345678901',
                'bic' => 'BREXPLPW',
                'account_name' => 'Nordic Logistics Billing',
                'is_active' => true,
                'is_primary' => true,
                'created_at' => $now->copy()->subDays(17),
                'updated_at' => $now->copy()->subDays(4),
            ],
            [
                'id' => (string) Str::uuid(),
                'address_uuid' => $getAddressId($clientIds[2]),
                'client_uuid' => $clientIds[2],
                'name' => 'Santander Bank',
                'number' => '10901012340000056789012345',
                'swift_code' => 'WBKPPLPP',
                'iban' => 'PL10901012340000056789012345',
                'bic' => 'WBKPPLPP',
                'account_name' => 'Jan Malinowski Private',
                'is_active' => true,
                'is_primary' => true,
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(1),
            ],
        ];

        DB::table('company_accounts')->insert($accounts);

        $this->command->info('CRM company accounts seeded successfully!');
    }
}
