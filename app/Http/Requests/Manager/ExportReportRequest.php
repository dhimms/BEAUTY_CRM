<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class ExportReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'report_type' => 'required|in:sales-performance,revenue,lost-reasons,lead-sources',
            'format' => 'nullable|in:xlsx,csv',
        ];
    }

    public function messages(): array
    {
        return [
            'report_type.required' => 'Tipe report wajib dipilih.',
            'report_type.in' => 'Tipe report tidak valid.',
        ];
    }
}
