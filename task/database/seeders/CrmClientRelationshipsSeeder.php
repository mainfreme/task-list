<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrmClientRelationshipsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientIds = DB::table('clients')->pluck('id')->toArray();

        if (count($clientIds) < 2) {
            $this->command->warn('Not enough clients found. Skipping CRM client relationships seeding.');
            return;
        }

        $now = now();

        $relationships = [
            [
                'id' => (string) Str::uuid(),
                'parent_uuid' => $clientIds[0],
                'child_uuid' => $clientIds[1],
                'relationship_type' => 'partner',
                'notes' => 'Joint projects in logistics domain.',
                'created_at' => $now->copy()->subDays(11),
                'updated_at' => $now->copy()->subDays(4),
            ],
            [
                'id' => (string) Str::uuid(),
                'parent_uuid' => $clientIds[0],
                'child_uuid' => $clientIds[2],
                'relationship_type' => 'subsidiary',
                'notes' => 'Potential subsidiary once the contract is signed.',
                'created_at' => $now->copy()->subDays(8),
                'updated_at' => $now->copy()->subDays(2),
            ],
        ];

        DB::table('client_relationships')->insert($relationships);

        $this->command->info('CRM client relationships seeded successfully!');
    }
}
