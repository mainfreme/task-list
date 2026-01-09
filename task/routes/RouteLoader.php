<?php

declare(strict_types=1);

namespace Routes;

use Illuminate\Support\Facades\File;

final class RouteLoader
{
    public static function loadApiRoutes(): void
    {
        self::loadRoutes('api.php');
    }

    public static function loadWebRoutes(): void
    {
        self::loadRoutes('web.php');
    }

    public static function loadCommandsRoutes(): void
    {
        self::loadRoutes('commands.php');
    }

    private static function loadRoutes(string $routeFile): void
    {
        $modulesPath = base_path('src/App');
        
        if (!File::isDirectory($modulesPath)) {
            return;
        }

        // Pobierz wszystkie foldery z src/App (to są moduły)
        $moduleDirectories = File::directories($modulesPath);
        
        foreach ($moduleDirectories as $modulePath) {
            $moduleName = basename($modulePath);
            
            if ($moduleName === 'Shared') {
                continue;
            }
            
            $routesFilePath = $modulePath . '/UI/Http/routes/' . $routeFile;
            
            if (File::exists($routesFilePath)) {
                require_once $routesFilePath;
            }
        }
    }
}