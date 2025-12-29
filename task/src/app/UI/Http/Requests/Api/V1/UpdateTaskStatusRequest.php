<?php

declare(strict_types=1);

namespace App\UI\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateTaskStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:pending,in_progress,completed,cancelled',
        ];
    }
}
