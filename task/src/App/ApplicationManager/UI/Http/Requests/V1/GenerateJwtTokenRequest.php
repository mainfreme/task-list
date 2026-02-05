<?php

declare(strict_types=1);

namespace App\ApplicationManager\UI\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

final class GenerateJwtTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expiration_minutes' => 'nullable|integer|min:1|max:525600', // Max 1 year
        ];
    }

    public function messages(): array
    {
        return [
            'expiration_minutes.integer' => 'Expiration minutes must be an integer',
            'expiration_minutes.min' => 'Expiration minutes must be at least 1',
            'expiration_minutes.max' => 'Expiration minutes cannot exceed 525600 (1 year)',
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
