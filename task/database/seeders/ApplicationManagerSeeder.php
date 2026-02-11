<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApplicationManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $applications = [
            [
                'id' => (string) Str::uuid(),
                'name' => 'Task Management App',
                'api_key_hash' => Hash::make(Str::random(32)),
                'request_url' => 'https://softwellhouse.pl/',
                'is_active' => true,
                'ip_whitelist' => json_encode(['172.18.0.5', '*']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Project Dashboard',
                'api_key_hash' => Hash::make(Str::random(32)),
                'request_url' => 'https://dashboard.example.com',
                'is_active' => true,
                'ip_whitelist' => json_encode(['172.16.0.0/12']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Mobile Task App',
                'api_key_hash' => Hash::make(Str::random(32)),
                'request_url' => 'https://mobile.example.com',
                'is_active' => true,
                'ip_whitelist' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Admin Panel',
                'api_key_hash' => Hash::make(Str::random(32)),
                'request_url' => 'https://admin.example.com',
                'is_active' => true,
                'ip_whitelist' => json_encode(['127.0.0.1', '::1']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'API Client Test',
                'api_key_hash' => Hash::make(Str::random(32)),
                'request_url' => null,
                'is_active' => false,
                'ip_whitelist' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('applications')->insert($applications);

        $this->command->info('Application managers seeded successfully!');
    }
}
