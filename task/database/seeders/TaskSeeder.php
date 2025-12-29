<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing application IDs
        $applicationIds = DB::table('applications')->pluck('id')->toArray();

        $tasks = [
            [
                'title' => 'Implementacja systemu uwierzytelniania',
                'website_url' => 'https://project.example.com/auth',
                'description' => 'Implementacja pełnego systemu uwierzytelniania użytkowników z obsługą OAuth2, JWT i dwuskładnikowej autentyfikacji.',
                'phone' => '+48 123 456 789',
                'email' => 'contact@project.example.com',
                'address' => 'ul. Główna 123, 00-001 Warszawa, Polska',
                'status' => 'in_progress',
                'application_manager_id' => $applicationIds[0] ?? null,
                'due_date' => Carbon::now()->addDays(14),
                'delivery_address' => 'ul. Projektowa 45, 00-002 Warszawa, Polska',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'title' => 'Optymalizacja wydajności bazy danych',
                'website_url' => 'https://dashboard.example.com/db-optimization',
                'description' => 'Optymalizacja zapytań SQL, dodanie indeksów i implementacja cache dla poprawy wydajności systemu.',
                'phone' => '+48 987 654 321',
                'email' => 'admin@dashboard.example.com',
                'address' => 'ul. Techniczna 67, 30-001 Kraków, Polska',
                'status' => 'pending',
                'application_manager_id' => $applicationIds[1] ?? null,
                'due_date' => Carbon::now()->addDays(7),
                'delivery_address' => 'ul. Serwerowa 12, 30-002 Kraków, Polska',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'title' => 'Aktualizacja interfejsu mobilnego',
                'website_url' => 'https://mobile.example.com/ui-update',
                'description' => 'Modernizacja interfejsu użytkownika aplikacji mobilnej zgodnie z najnowszymi wytycznymi Material Design.',
                'phone' => '+48 555 123 456',
                'email' => 'mobile@company.example.com',
                'address' => 'ul. Mobilna 89, 50-001 Wrocław, Polska',
                'status' => 'completed',
                'application_manager_id' => $applicationIds[2] ?? null,
                'due_date' => Carbon::now()->subDays(3),
                'delivery_address' => 'ul. Aplikacji 34, 50-002 Wrocław, Polska',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'title' => 'Implementacja systemu powiadomień',
                'website_url' => 'https://admin.example.com/notifications',
                'description' => 'Implementacja systemu powiadomień push, email i SMS dla użytkowników systemu administracyjnego.',
                'phone' => '+48 777 888 999',
                'email' => 'notifications@admin.example.com',
                'address' => 'ul. Administracyjna 156, 60-001 Poznań, Polska',
                'status' => 'pending',
                'application_manager_id' => $applicationIds[3] ?? null,
                'due_date' => Carbon::now()->addDays(21),
                'delivery_address' => 'ul. Systemowa 78, 60-002 Poznań, Polska',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Migracja danych do nowej struktury',
                'website_url' => 'https://legacy.example.com/migration',
                'description' => 'Migracja danych z systemu legacy do nowej architektury bazy danych z zachowaniem integralności danych.',
                'phone' => '+48 444 333 222',
                'email' => 'migration@legacy.example.com',
                'address' => 'ul. Dziedzictwa 234, 40-001 Katowice, Polska',
                'status' => 'in_progress',
                'application_manager_id' => null, // Zadanie bez przypisanej aplikacji
                'due_date' => Carbon::now()->addDays(30),
                'delivery_address' => 'ul. Nowoczesna 56, 40-002 Katowice, Polska',
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'title' => 'Implementacja API dokumentacji',
                'website_url' => 'https://api.example.com/docs',
                'description' => 'Utworzenie kompleksowej dokumentacji API z przykładami użycia i automatycznym generowaniem specyfikacji OpenAPI.',
                'phone' => '+48 666 777 888',
                'email' => 'api@docs.example.com',
                'address' => 'ul. Dokumentacyjna 78, 80-001 Gdańsk, Polska',
                'status' => 'pending',
                'application_manager_id' => $applicationIds[0] ?? null,
                'due_date' => Carbon::now()->addDays(10),
                'delivery_address' => 'ul. Specyfikacji 90, 80-002 Gdańsk, Polska',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Testowanie bezpieczeństwa aplikacji',
                'website_url' => 'https://security.example.com/testing',
                'description' => 'Przeprowadzenie kompleksowych testów bezpieczeństwa aplikacji webowej, w tym testów penetracyjnych i analizy podatności.',
                'phone' => '+48 999 000 111',
                'email' => 'security@testing.example.com',
                'address' => 'ul. Bezpieczna 345, 70-001 Szczecin, Polska',
                'status' => 'completed',
                'application_manager_id' => $applicationIds[1] ?? null,
                'due_date' => Carbon::now()->subDays(5),
                'delivery_address' => 'ul. Audytowa 67, 70-002 Szczecin, Polska',
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(4),
            ],
            [
                'title' => 'Optymalizacja obrazów i mediów',
                'website_url' => 'https://media.example.com/optimization',
                'description' => 'Implementacja automatycznej optymalizacji obrazów i plików multimedialnych dla poprawy wydajności ładowania strony.',
                'phone' => '+48 222 333 444',
                'email' => 'media@optimization.example.com',
                'address' => 'ul. Multimedialna 123, 90-001 Łódź, Polska',
                'status' => 'pending',
                'application_manager_id' => null, // Zadanie bez przypisanej aplikacji
                'due_date' => Carbon::now()->addDays(5),
                'delivery_address' => 'ul. Wydajnościowa 45, 90-002 Łódź, Polska',
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(1),
            ],
        ];

        DB::table('tasks')->insert($tasks);

        $this->command->info('Tasks seeded successfully!');
    }
}
