<?php

return  [
  'barryvdh/laravel-ide-helper' =>
   [
    'providers' =>
     [
      0 => 'Barryvdh\\LaravelIdeHelper\\IdeHelperServiceProvider',
    ],
  ],
  'darkaonline/l5-swagger' =>
   [
    'aliases' =>
     [
      'L5Swagger' => 'L5Swagger\\L5SwaggerFacade',
    ],
    'providers' =>
     [
      0 => 'L5Swagger\\L5SwaggerServiceProvider',
    ],
  ],
  'laravel/sail' =>
   [
    'providers' =>
     [
      0 => 'Laravel\\Sail\\SailServiceProvider',
    ],
  ],
  'laravel/sanctum' =>
   [
    'providers' =>
     [
      0 => 'Laravel\\Sanctum\\SanctumServiceProvider',
    ],
  ],
  'laravel/telescope' =>
   [
    'providers' =>
     [
      0 => 'Laravel\\Telescope\\TelescopeServiceProvider',
    ],
  ],
  'laravel/tinker' =>
   [
    'providers' =>
     [
      0 => 'Laravel\\Tinker\\TinkerServiceProvider',
    ],
  ],
  'nesbot/carbon' =>
   [
    'providers' =>
     [
      0 => 'Carbon\\Laravel\\ServiceProvider',
    ],
  ],
  'nunomaduro/collision' =>
   [
    'providers' =>
     [
      0 => 'NunoMaduro\\Collision\\Adapters\\Laravel\\CollisionServiceProvider',
    ],
  ],
  'nunomaduro/termwind' =>
   [
    'providers' =>
     [
      0 => 'Termwind\\Laravel\\TermwindServiceProvider',
    ],
  ],
  'sentry/sentry-laravel' =>
   [
    'aliases' =>
     [
      'Sentry' => 'Sentry\\Laravel\\Facade',
    ],
    'providers' =>
     [
      0 => 'Sentry\\Laravel\\ServiceProvider',
      1 => 'Sentry\\Laravel\\Tracing\\ServiceProvider',
    ],
  ],
  'spatie/laravel-data' =>
   [
    'providers' =>
     [
      0 => 'Spatie\\LaravelData\\LaravelDataServiceProvider',
    ],
  ],
  'spatie/laravel-fractal' =>
   [
    'aliases' =>
     [
      'Fractal' => 'Spatie\\Fractal\\Facades\\Fractal',
    ],
    'providers' =>
     [
      0 => 'Spatie\\Fractal\\FractalServiceProvider',
    ],
  ],
  'spatie/laravel-ignition' =>
   [
    'aliases' =>
     [
      'Flare' => 'Spatie\\LaravelIgnition\\Facades\\Flare',
    ],
    'providers' =>
     [
      0 => 'Spatie\\LaravelIgnition\\IgnitionServiceProvider',
    ],
  ],
  'spatie/laravel-permission' =>
   [
    'providers' =>
     [
      0 => 'Spatie\\Permission\\PermissionServiceProvider',
    ],
  ],
  'spatie/php-structure-discoverer' =>
   [
    'providers' =>
     [
      0 => 'Spatie\\StructureDiscoverer\\StructureDiscovererServiceProvider',
    ],
  ],
  'tymon/jwt-auth' =>
   [
    'aliases' =>
     [
      'JWTAuth' => 'Tymon\\JWTAuth\\Facades\\JWTAuth',
      'JWTFactory' => 'Tymon\\JWTAuth\\Facades\\JWTFactory',
    ],
    'providers' =>
     [
      0 => 'Tymon\\JWTAuth\\Providers\\LaravelServiceProvider',
    ],
  ],
];
