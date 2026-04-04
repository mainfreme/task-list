<?php

declare(strict_types=1);

namespace App\Ops\UI\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class DeployErrorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'project' => ['required', 'string', 'max:255'],
            'repository' => ['required', 'string', 'max:500'],
            'container' => ['nullable', 'string', 'max:255'],
            'stage' => ['required', 'string', 'in:build,up,status'],
            'message' => ['required_without:error', 'nullable', 'string', 'max:10000'],
            'error' => ['required_without:message', 'nullable', 'string', 'max:10000'],
            'hostname' => ['nullable', 'string', 'max:255'],
        ];
    }
}
