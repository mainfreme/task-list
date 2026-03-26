<?php

declare(strict_types=1);

namespace App\Settings\Infrastructure\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class SettingEntryModel extends Model
{
    use HasUuids;

    protected $table = 'setting_entries';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'group_key',
        'field_key',
        'field_type',
        'value',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
