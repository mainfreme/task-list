<?php

declare(strict_types=1);

namespace App\Crm\Infrastructure\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CrmClientTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientIds = DB::table('clients')->pluck('id')->toArray();
        $tagIds = DB::table('client_tags')->pluck('id')->toArray();

        if (empty($clientIds) || empty($tagIds)) {
            $this->command->warn('No clients or tags found. Skipping CRM client_tag seeding.');
            return;
        }

        $now = now();

        $pivotRows = [
            [
                'client_uuid' => $clientIds[0],
                'client_tag_uuid' => $tagIds[0],
                'created_at' => $now->copy()->subDays(15),
            ],
            [
                'client_uuid' => $clientIds[0],
                'client_tag_uuid' => $tagIds[1],
                'created_at' => $now->copy()->subDays(14),
            ],
            [
                'client_uuid' => $clientIds[1],
                'client_tag_uuid' => $tagIds[1],
                'created_at' => $now->copy()->subDays(12),
            ],
            [
                'client_uuid' => $clientIds[2],
                'client_tag_uuid' => $tagIds[2],
                'created_at' => $now->copy()->subDays(5),
            ],
        ];

        DB::table('client_tag')->insert($pivotRows);

        $this->command->info('CRM client_tag pivot seeded successfully!');
    }
}
