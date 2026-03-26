<?php

declare(strict_types=1);

namespace App\Settings\UI\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

final class SetIntegrationAccountEnabledRequest extends FormRequest
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
            'enabled' => ['required', 'boolean'],
        ];
    }
}
