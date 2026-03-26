<?php

declare(strict_types=1);

namespace App\Crm\UI\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

final class CreateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'nip' => 'required|string|max:20',
            'country' => 'required|string|max:100',
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
