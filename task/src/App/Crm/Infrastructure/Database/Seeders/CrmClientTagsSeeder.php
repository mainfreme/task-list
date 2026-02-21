<?php

declare(strict_types=1);

namespace App\Crm\Infrastructure\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrmClientTagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $tags = [
            [
                'id' => (string) Str::uuid(),
                'name' => 'Strategic',
                'color' => '#1E88E5',
                'description' => 'High-priority clients with long-term potential.',
                'created_at' => $now->copy()->subDays(20),
                'updated_at' => $now->copy()->subDays(2),
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Logistics',
                'color' => '#43A047',
                'description' => 'Clients in the logistics and transport sector.',
                'created_at' => $now->copy()->subDays(18),
                'updated_at' => $now->copy()->subDays(3),
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Inbound',
                'color' => '#FB8C00',
                'description' => 'Leads captured via web forms and campaigns.',
                'created_at' => $now->copy()->subDays(10),
                'updated_at' => $now->copy()->subDays(1),
            ],
        ];

        DB::table('client_tags')->insert($tags);

        $this->command->info('CRM client tags seeded successfully!');
    }
}
