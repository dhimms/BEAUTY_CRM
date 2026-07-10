<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class QualifyLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'qualification' => 'required|in:qualified,unqualified,not_fit',
            'notes'         => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'qualification.required' => 'Qualification wajib dipilih.',
            'qualification.in'       => 'Qualification tidak valid.',
        ];
    }
}
