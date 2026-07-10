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
            'value'               => 'required|numeric|min:0',
            'expected_close_date' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Nama deal wajib diisi.',
            'value.required' => 'Nilai deal wajib diisi.',
            'value.numeric'  => 'Nilai deal harus berupa angka.',
        ];
    }
}
