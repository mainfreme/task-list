<?php

declare(strict_types=1);

namespace App\Settings\UI\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateChartDefinitionRequest extends FormRequest
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
            'chart_type' => ['required', 'string', 'max:255'],
            'display_fields' => ['required', 'array'],
            'sql_query' => ['required', 'string', 'min:1'],
        ];
    }
}
