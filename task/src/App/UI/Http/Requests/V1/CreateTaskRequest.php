<?php

declare(strict_types=1);

namespace App\UI\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

final class CreateTaskRequest extends FormRequest
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
            'phone' => 'required_without_all:email|string|max:50',
            'email' => 'required_without_all:phone|email|max:255',
            'address' => 'required|string',
            'due_date' => 'nullable|date',
            'delivery_address' => 'nullable|string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
