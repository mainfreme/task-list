<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class TaskModel extends Model
{
    use HasUuids;

    protected $table = 'tasks';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'title',
        'website_url',
        'description',
        'phone',
        'email',
        'address',
        'status',
        'application_manager_id',
        'user_id',
        'due_date',
        'delivery_address',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
