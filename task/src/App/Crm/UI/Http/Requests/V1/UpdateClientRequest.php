<?php

declare(strict_types=1);

namespace App\Crm\UI\Http\Requests\V1;

use App\Crm\Domain\ValueObject\Nip;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

final class UpdateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'nip' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value === null || $value === '') {
                        return;
                    }
                    if (!Nip::isValid((string) $value)) {
                        $fail(Nip::INVALID_MESSAGE);
                    }
                },
            ],
            'country' => 'sometimes|string|max:100',
            'is_company' => 'sometimes|boolean',
            'regon' => 'nullable|string|max:20',
            'pesel' => 'nullable|string|max:11',
            'source' => 'nullable|string|max:255',
            'rating' => 'nullable|integer|min:1|max:5',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:lead,prospect,active,inactive,archived',
            'address_uuid' => 'nullable|string|uuid',
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
