<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CrmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CrmUsersSeeder::class,
            CrmClientsSeeder::class,
            CrmAddressesSeeder::class,
            CrmClientContactsSeeder::class,
            CrmClientNotesSeeder::class,
            CrmClientTagsSeeder::class,
            CrmClientTagSeeder::class,
            CrmClientRelationshipsSeeder::class,
            CrmCompanyAccountsSeeder::class,
        ]);
    }
}
