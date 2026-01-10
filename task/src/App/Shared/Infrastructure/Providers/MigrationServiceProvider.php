<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

final class MigrationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     * Automatically loads migrations from all modules that have Database/Migrations directory.
     */
    public function boot(): void
    {
        $modulesPath = base_path('src/App');
        
        if (!File::isDirectory($modulesPath)) {
            return;
        }

        // Get all module directories
        $moduleDirectories = File::directories($modulesPath);

        foreach ($moduleDirectories as $modulePath) {
            $moduleName = basename($modulePath);
            
            // Skip Shared module (it doesn't have its own migrations)
            if ($moduleName === 'Shared') {
                continue;
            }

            // Check if module has Database/Migrations directory in Infrastructure
            $migrationsPath = $modulePath . '/Infrastructure/Database/Migrations';
            
            if (File::isDirectory($migrationsPath)) {
                $this->loadMigrationsFrom($migrationsPath);
            }
        }
    }
}
