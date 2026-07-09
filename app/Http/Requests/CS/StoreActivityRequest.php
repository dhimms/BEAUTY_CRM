<?php

namespace App\Http\Requests\CS;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'activitable_type' => 'required|in:customer,ticket',
            'activitable_id' => 'required|integer',
            'type' => 'required|in:' . implode(',', array_keys(config('beauty-crm.activity_types'))),
            'subject' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'duration' => 'nullable|string|max:20',
            'result' => 'nullable|in:' . implode(',', array_keys(config('beauty-crm.activity_results'))),
            'activity_date' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'activitable_type.required' => 'Tipe entitas wajib dipilih.',
            'activitable_id.required' => 'ID entitas wajib diisi.',
            'type.required' => 'Tipe aktivitas wajib dipilih.',
        ];
    }
}
