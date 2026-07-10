<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'            => 'required|in:call,whatsapp,email,meeting,note,other',
            'subject'         => 'nullable|string|max:255',
            'description'     => 'nullable|string|max:2000',
            'duration'        => 'nullable|string|max:20',
            'result'          => 'nullable|string|max:100',
            'activity_date'   => 'nullable|date',
            'follow_up_date'  => 'nullable|date',
            'follow_up_type'  => 'nullable|in:call,whatsapp,email,meeting',
            'follow_up_notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Tipe aktivitas wajib diisi.',
            'type.in'       => 'Tipe aktivitas tidak valid.',
        ];
    }
}
