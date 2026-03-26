<?php

declare(strict_types=1);

namespace App\Task\UI\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

final class RecordTaskTimeSessionRequest extends FormRequest
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
            'action' => ['required', 'string', 'in:start,pause,stop'],
        ];
    }
}
