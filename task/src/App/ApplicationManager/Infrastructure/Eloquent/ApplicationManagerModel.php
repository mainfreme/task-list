<?php

declare(strict_types=1);

namespace App\ApplicationManager\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class ApplicationManagerModel extends Model
{
    use HasUuids;

    protected $table = 'applications';

    protected $keyType = 'string';

    public $incrementing = false;

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
