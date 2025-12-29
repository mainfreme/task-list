<?php

declare(strict_types=1);

namespace App\UI\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'website_url' => 'required|string|url|max:255',
            'description' => 'required|string',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'due_date' => 'nullable|date',
            'delivery_address' => 'nullable|string',
        ];
    }
}
