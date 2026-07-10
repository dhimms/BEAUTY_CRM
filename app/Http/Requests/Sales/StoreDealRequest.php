<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class StoreDealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lead_id'             => 'required|exists:leads,id',
            'name'                => 'required|string|max:255',
            'value'               => 'required|numeric|min:0',
            'pipeline_stage_id'   => 'nullable|exists:pipeline_stages,id',
            'expected_close_date' => 'nullable|date|after_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'lead_id.required'  => 'Lead wajib dipilih.',
            'lead_id.exists'    => 'Lead tidak ditemukan.',
            'name.required'     => 'Nama deal wajib diisi.',
            'value.required'    => 'Nilai deal wajib diisi.',
            'value.numeric'     => 'Nilai deal harus berupa angka.',
            'value.min'         => 'Nilai deal minimal 0.',
            'expected_close_date.after_or_equal' => 'Expected close date harus hari ini atau setelahnya.',
        ];
    }
}
