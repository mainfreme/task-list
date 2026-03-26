<?php

declare(strict_types=1);

namespace App\Settings\Infrastructure\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

final class ChartDefinitionModel extends Model
{
    use HasUuids;

    protected $table = 'chart_definitions';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'chart_type',
        'display_fields',
        'sql_query',
    ];

    protected $casts = [
        'display_fields' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
