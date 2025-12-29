<?php

declare(strict_types=1);

namespace App\UI\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class StoreApplicationManagerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'request_url' => 'nullable|string|url|max:255',
            'is_active' => 'sometimes|boolean',
            'ip_whitelist' => 'nullable|array',
            'ip_whitelist.*' => 'ip',
        ];
    }
}
