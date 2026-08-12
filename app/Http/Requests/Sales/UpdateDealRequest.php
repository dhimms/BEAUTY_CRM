<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                => 'required|string|max:255',
            'expected_close_date' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Nama deal wajib diisi.',
        ];
    }
}
