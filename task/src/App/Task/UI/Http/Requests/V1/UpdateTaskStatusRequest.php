<?php

declare(strict_types=1);

namespace App\Task\UI\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateTaskStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => 'required|string|in:pending,in_progress,completed,cancelled',
        ];
    }
}
