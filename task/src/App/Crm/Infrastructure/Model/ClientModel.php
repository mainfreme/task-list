<?php

declare(strict_types=1);

namespace App\Crm\Infrastructure\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class ClientModel extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $table = 'clients';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'address_uuid',
        'name',
        'nip',
        'regon',
        'pesel',
        'country',
        'status',
        'source',
        'rating',
        'last_contacted_at',
        'next_contact_at',
        'notes',
        'is_delete',
        'is_company',
    ];

    protected $casts = [
        'last_contacted_at' => 'datetime',
        'next_contact_at' => 'datetime',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
