<?php

declare(strict_types=1);

namespace App\Settings\UI\Http\Requests\V1;

use App\Settings\Domain\ValueObject\SettingFieldType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpsertSettingEntryRequest extends FormRequest
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
        $types = array_map(static fn (SettingFieldType $t) => $t->value, SettingFieldType::cases());

        return [
            'group_key' => ['required', 'string', 'max:255'],
            'field_key' => ['required', 'string', 'max:255'],
            'field_type' => ['required', 'string', Rule::in($types)],
            'value' => ['nullable', 'string'],
        ];
    }
}
