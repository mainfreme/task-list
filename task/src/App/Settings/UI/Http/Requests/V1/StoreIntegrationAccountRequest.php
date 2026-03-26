<?php

declare(strict_types=1);

namespace App\Settings\UI\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

final class StoreIntegrationAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'enabled' => ['sometimes', 'boolean'],
            'external_account_id' => ['nullable', 'string', 'max:255'],
            'provider' => ['nullable', 'string', 'max:64'],
            'credentials' => ['required', 'array'],
        ];
    }
}
