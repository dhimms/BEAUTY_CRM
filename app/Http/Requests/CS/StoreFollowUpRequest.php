<?php

namespace App\Http\Requests\CS;

use Illuminate\Foundation\Http\FormRequest;

class StoreFollowUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'follow_up_date' => 'required|date|after_or_equal:today',
            'follow_up_type' => 'required|in:call,whatsapp,email,meeting',
            'follow_up_notes' => 'nullable|string|max:2000',
            'subject' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer wajib dipilih.',
            'follow_up_date.required' => 'Tanggal follow-up wajib diisi.',
            'follow_up_date.after_or_equal' => 'Tanggal follow-up tidak boleh di masa lalu.',
            'follow_up_type.required' => 'Tipe follow-up wajib dipilih.',
        ];
    }
}
