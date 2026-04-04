<?php

declare(strict_types=1);

namespace App\Ops\Infrastructure\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class DeployFailureModel extends Model
{
    use HasUuids;

    protected $table = 'deploy_failures';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'project',
        'repository',
        'container',
        'stage',
        'message',
        'hostname',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
