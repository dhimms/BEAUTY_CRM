<?php

namespace App\Http\Requests\CS;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'title' => 'required|string|max:255',
            'category' => 'nullable|in:' . implode(',', array_keys(config('beauty-crm.ticket_categories'))),
            'priority' => 'required|in:' . implode(',', array_keys(config('beauty-crm.ticket_priorities'))),
            'description' => 'nullable|string|max:5000',
            'assigned_to' => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer wajib dipilih.',
            'customer_id.exists' => 'Customer tidak ditemukan.',
            'title.required' => 'Judul ticket wajib diisi.',
            'priority.required' => 'Prioritas wajib dipilih.',
        ];
    }
}
