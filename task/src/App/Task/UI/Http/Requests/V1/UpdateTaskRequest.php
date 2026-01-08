<?php

declare(strict_types=1);

namespace App\Task\UI\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateTaskRequest extends FormRequest
{
    // public function authorize(): bool
    // {
    //     return true;
    // }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'website_url' => 'sometimes|string|url|max:255',
            'description' => 'sometimes|string',
            'phone' => 'required_without_all:email|string|max:50',
            'email' => 'required_without_all:phone|email|max:255',
            'address' => 'sometimes|string',
            'due_date' => 'nullable|date',
            'delivery_address' => 'nullable|string',
        ];
    }
}
