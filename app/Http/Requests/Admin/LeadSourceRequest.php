<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LeadSourceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'icon'        => ['nullable', 'string', 'max:10'],
            'color'       => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['boolean'],
        ];
    }
}
