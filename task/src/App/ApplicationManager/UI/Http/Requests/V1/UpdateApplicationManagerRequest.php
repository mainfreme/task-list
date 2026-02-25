<?php

declare(strict_types=1);

namespace App\ApplicationManager\UI\Http\Requests\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

final class UpdateApplicationManagerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'request_url' => 'nullable|string|url|max:255',
            'is_active' => 'sometimes|boolean',
            'ip_whitelist' => 'nullable|array',
            'ip_whitelist.*' => ['required', 'string', function (string $attribute, mixed $value, \Closure $fail): void {
                if ($value === '*') {
                    return;
                }
                if (filter_var($value, FILTER_VALIDATE_IP) !== false) {
                    return;
                }
                if (str_contains($value, '/')) {
                    [$ip, $prefix] = explode('/', $value, 2);
                    if ($ip !== '' && $prefix !== '' && ctype_digit($prefix)) {
                        $prefixInt = (int) $prefix;
                        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false && $prefixInt >= 0 && $prefixInt <= 32) {
                            return;
                        }
                        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false && $prefixInt >= 0 && $prefixInt <= 128) {
                            return;
                        }
                    }
                }
                $fail('Each IP whitelist entry must be a valid IP address, CIDR, or *');
            }],
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'Application name cannot exceed 255 characters',
            'request_url.url' => 'Request URL must be a valid URL',
            'is_active.boolean' => 'Active status must be a boolean',
            'ip_whitelist.array' => 'IP whitelist must be an array',
            'ip_whitelist.*' => 'Each IP whitelist entry must be a valid IP address, CIDR, or *',
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
