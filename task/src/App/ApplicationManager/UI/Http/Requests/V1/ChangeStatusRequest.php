<?php

declare(strict_types=1);

namespace App\ApplicationManager\UI\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

final class ChangeStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'is_active' => 'required|boolean',
        ];
    }
}