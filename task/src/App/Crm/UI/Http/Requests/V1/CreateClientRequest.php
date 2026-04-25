<?php

declare(strict_types=1);

namespace App\Crm\UI\Http\Requests\V1;

use App\Crm\Domain\ValueObject\Nip;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

final class CreateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('is_company')) {
            $this->merge(['is_company' => true]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'nip' => [
                'nullable',
                'string',
                'max:20',
                'required_if:is_company,true',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value === null || $value === '') {
                        return;
                    }
                    if (!Nip::isValid((string) $value)) {
                        $fail(Nip::INVALID_MESSAGE);
                    }
                },
            ],
            'country' => 'required|string|max:100',
            'is_company' => 'sometimes|boolean',
            'regon' => 'nullable|string|max:20',
            'pesel' => 'nullable|string|max:11|required_if:is_company,false',
            'source' => 'nullable|string|max:255',
            'rating' => 'nullable|integer|min:1|max:5',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:lead,prospect,active,inactive,archived',
            'address_uuid' => 'nullable|string|uuid',
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
