<?php

declare(strict_types=1);

namespace Infrastructure\Task\Eloquent;

use Illuminate\Database\Eloquent\Model;

final class TaskModel extends Model
{
    protected $table = 'tasks';

    protected $fillable = [
        'title',
        'website_url',
        'description',
        'phone',
        'email',
        'address',
        'status',
        'application_manager_id',
        'due_date',
        'delivery_address',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

