<?php

use Illuminate\Support\Facades\Route;

// Alias: api/document -> api/documentation (Swagger UI)
Route::redirect('/document', '/api/documentation', 301);

require_once __DIR__ . '/RouteLoader.php';

\Routes\RouteLoader::loadApiRoutes();