<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Deploy webhook shared secret
    |--------------------------------------------------------------------------
    |
    | Bearer token for POST /api/v1/deploy/error (CI/deploy scripts).
    | Must match DEPLOY_NOTIFY_TOKEN on the machine running deploy-frontends.sh.
    |
    */
    'webhook_secret' => env('DEPLOY_WEBHOOK_SECRET'),
];
