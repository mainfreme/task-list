<?php

declare(strict_types=1);

namespace App\Crm\Infrastructure\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class AddressModel extends Model
{
    use HasUuids;

    protected $table = 'addresses';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'client_uuid',
        'street',
        'postal_code',
        'city',
        'state_province',
        'country',
        'additional_info',
        'house_number',
        'apartment_number',
        'type',
        'is_primary',
        'is_active',
        'latitude',
        'longitude',
        'added_at',
    ];

    protected $casts = [
        'added_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(ClientModel::class, 'client_uuid', 'id');
    }
}
