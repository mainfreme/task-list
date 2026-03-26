<?php

declare(strict_types=1);

namespace App\Settings\Infrastructure\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class IntegrationAccountModel extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $table = 'integration_accounts';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'enabled',
        'external_account_id',
        'provider',
        'credentials',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'credentials' => 'encrypted:array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
