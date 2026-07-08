<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PipelineStageRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'color'       => ['nullable', 'string', 'max:20'],
            'order'       => ['nullable', 'integer', 'min:0'],
            'probability' => ['required', 'integer', 'min:0', 'max:100'],
        ];
    }
}
