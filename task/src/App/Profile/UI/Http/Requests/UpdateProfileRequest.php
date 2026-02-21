<?php

declare(strict_types=1);

namespace App\Profile\UI\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

final class UpdateProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'avatar' => 'required|string|max:255',
            'birth_date' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Imie jest wymagane',
            'first_name.max' => 'Imie nie może przekroczyć 100 znaków',
            'last_name.required' => 'Nazwisko jest wymagane',
            'last_name.max' => 'Nazwisko nie może przekroczyć 100 znaków',
            'phone.required' => 'Telefon jest wymagany',
            'phone.max' => 'Telefon nie może przekroczyć 30 znaków',
            'avatar.required' => 'Avatar jest wymagany',
            'avatar.max' => 'Avatar nie może przekroczyć 255 znaków',
            'birth_date.required' => 'Data urodzenia jest wymagana',
            'birth_date.date' => 'Data urodzenia musi być w formacie YYYY-MM-DD',
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