<?php

declare(strict_types=1);

namespace App\Auth\Infrastructure\Model;

use App\Profile\Infrastructure\Model\ProfileModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

final class UserModel extends Model
{
    use HasUuids;

    protected $table = 'users';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'roles',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'roles' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function profile(): HasOne
    {
        return $this->hasOne(ProfileModel::class);
    }
}
