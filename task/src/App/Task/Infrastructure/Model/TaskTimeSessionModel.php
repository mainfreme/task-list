<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\Model;

use Illuminate\Database\Eloquent\Model;

final class TaskTimeSessionModel extends Model
{
    protected $table = 'task_time_sessions';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'task_id',
        'user_id',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];
}
