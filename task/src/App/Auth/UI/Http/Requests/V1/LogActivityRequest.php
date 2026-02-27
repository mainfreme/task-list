<?php

declare(strict_types=1);

namespace App\Auth\UI\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

final class LogActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => 'required|string|max:2048',
            'ip_address' => 'required|ip',
            'user_agent' => 'required|string|max:512',
            'action' => 'required|string|max:64',
            'metadata' => 'sometimes|array',
        ];
    }

    public function messages(): array
    {
        return [
            'url.required' => 'URL is required',
            'url.max' => 'URL cannot exceed 2048 characters',
            'ip_address.required' => 'IP address is required',
            'ip_address.ip' => 'Please provide a valid IP address',
            'user_agent.required' => 'User agent is required',
            'user_agent.max' => 'User agent cannot exceed 512 characters',
            'action.required' => 'Action is required',
            'action.max' => 'Action cannot exceed 64 characters',
            'metadata.array' => 'Metadata must be an array',
        ];
    }

    protected function failedValidation(Validator $validator): void
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
