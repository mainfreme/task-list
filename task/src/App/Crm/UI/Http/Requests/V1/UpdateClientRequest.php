<?php

declare(strict_types=1);

namespace App\Crm\UI\Http\Requests\V1;

use App\Crm\UI\Http\Rules\NipValue;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'nip' => ['sometimes', 'string', 'max:20', new NipValue()],
            'country' => 'sometimes|string|max:100',
            'is_company' => 'sometimes|boolean',
            'regon' => 'nullable|string|max:20',
            'pesel' => 'nullable|string|max:11',
            'source' => 'nullable|string|max:255',
            'rating' => 'nullable|integer|min:1|max:5',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:lead,prospect,active,inactive,archived',
            'address_uuid' => 'nullable|string|uuid',
        ];
    }
}
