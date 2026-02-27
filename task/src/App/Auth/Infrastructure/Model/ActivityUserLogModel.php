<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ActivityUserLogModel extends Model
{
    protected $table = 'activity_user_logs';

    protected $fillable = [
        'user_id',
        'url',
        'log_activity',
    ];

    protected $casts = [
        'log_activity' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'id');
    }
}
