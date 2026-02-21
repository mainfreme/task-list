<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Explicit seeder order per module (dependencies). If not defined, seeders run alphabetically.
     */
    private const MODULE_SEEDER_ORDER = [
        'Crm' => [
            'CrmUsersSeeder',
            'CrmClientsSeeder',
            'CrmClientTagsSeeder',
            'CrmAddressesSeeder',
            'CrmClientContactsSeeder',
            'CrmClientNotesSeeder',
            'CrmClientRelationshipsSeeder',
            'CrmClientTagSeeder',
            'CrmCompanyAccountsSeeder',
        ],
    ];

    public function run(): void
    {
        $modulesPath = base_path('src/App');
        if (!File::isDirectory($modulesPath)) {
            return;
        }

        foreach (File::directories($modulesPath) as $modulePath) {
            if (basename($modulePath) === 'Shared') {
                continue;
            }

            $moduleName = basename($modulePath);
            $seedersPath = $modulePath . '/Infrastructure/Database/Seeders';
            if (!File::isDirectory($seedersPath)) {
                continue;
            }

            $seeders = $this->getSeedersInOrder($modulePath, $moduleName);
            foreach ($seeders as $class) {
                if (class_exists($class)) {
                    $this->call($class);
                }
            }
        }
    }

    /**
     * @return list<string>
     */
    private function getSeedersInOrder(string $modulePath, string $moduleName): array
    {
        $order = self::MODULE_SEEDER_ORDER[$moduleName] ?? null;
        $namespace = 'App\\' . $moduleName . '\\Infrastructure\\Database\\Seeders\\';

        if ($order !== null) {
            return array_map(fn (string $name) => $namespace . $name, $order);
        }

        $files = File::files($seedersPath = $modulePath . '/Infrastructure/Database/Seeders');
        sort($files);

        return array_map(
            fn (\Symfony\Component\Finder\SplFileInfo $file) => $namespace . $file->getFilenameWithoutExtension(),
            $files
        );
    }
}
