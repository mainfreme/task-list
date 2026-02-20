<?php

declare(strict_types=1);

namespace App\Profile\Infrastructure\Model;

use App\Auth\Infrastructure\Model\UserModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileModel extends Model
{
    use HasUuids;

    protected $table = 'profiles';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'avatar',
        'birth_date',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class);
    }
}
