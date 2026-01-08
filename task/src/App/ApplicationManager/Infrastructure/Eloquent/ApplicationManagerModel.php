<?php

declare(strict_types=1);

namespace App\ApplicationManager\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

final class ApplicationManagerModel extends Model
{
    protected $table = 'applications';

    protected $fillable = [
        'name',
        'api_key_hash',
        'request_url',
        'is_active',
        'ip_whitelist',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'ip_whitelist' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
