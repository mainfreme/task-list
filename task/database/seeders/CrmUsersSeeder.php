<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CrmUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $users = [
            [
                'id' => (string) Str::uuid(),
                'email' => 'anna.kowalska@crm.local',
                'roles' => json_encode(['admin', 'manager']),
                'password' => Hash::make('secret123'),
                'remember_token' => Str::random(10),
                'created_at' => $now->copy()->subDays(20),
                'updated_at' => $now->copy()->subDays(2),
            ],
            [
                'id' => (string) Str::uuid(),
                'email' => 'tomasz.nowak@crm.local',
                'roles' => json_encode(['sales']),
                'password' => Hash::make('secret123'),
                'remember_token' => Str::random(10),
                'created_at' => $now->copy()->subDays(15),
                'updated_at' => $now->copy()->subDays(3),
            ],
            [
                'id' => (string) Str::uuid(),
                'email' => 'monika.zielinska@crm.local',
                'roles' => json_encode(['support']),
                'password' => Hash::make('secret123'),
                'remember_token' => Str::random(10),
                'created_at' => $now->copy()->subDays(10),
                'updated_at' => $now->copy()->subDays(1),
            ],
        ];

        DB::table('users')->insert($users);

        $this->command->info('CRM users seeded successfully!');
    }
}
