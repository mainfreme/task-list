<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrmClientNotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientIds = DB::table('clients')->pluck('id')->toArray();
        $userIds = DB::table('users')->pluck('id')->toArray();

        if (empty($clientIds) || empty($userIds)) {
            $this->command->warn('No clients or users found. Skipping CRM client notes seeding.');
            return;
        }

        $now = now();

        $notes = [
            [
                'id' => (string) Str::uuid(),
                'client_uuid' => $clientIds[0],
                'user_uuid' => $userIds[0],
                'content' => 'Kick-off meeting scheduled for next week.',
                'type' => 'meeting',
                'created_at' => $now->copy()->subDays(12),
                'updated_at' => $now->copy()->subDays(12),
            ],
            [
                'id' => (string) Str::uuid(),
                'client_uuid' => $clientIds[1],
                'user_uuid' => $userIds[1],
                'content' => 'Customer asked for updated pricing and timeline.',
                'type' => 'call',
                'created_at' => $now->copy()->subDays(7),
                'updated_at' => $now->copy()->subDays(6),
            ],
            [
                'id' => (string) Str::uuid(),
                'client_uuid' => $clientIds[2],
                'user_uuid' => $userIds[2],
                'content' => 'Sent onboarding email with next steps.',
                'type' => 'email',
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subDays(2),
            ],
        ];

        DB::table('client_notes')->insert($notes);

        $this->command->info('CRM client notes seeded successfully!');
    }
}
