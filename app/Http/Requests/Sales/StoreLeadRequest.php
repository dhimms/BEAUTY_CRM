<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'             => 'required|string|max:255',
            'phone'            => [
                'required',
                'string',
                'max:20',
                Rule::unique('leads', 'phone')->whereNull('deleted_at'),
                Rule::unique('customers', 'phone')->whereNull('deleted_at'),
            ],
            'email'            => [
                'required',
                'email',
                'max:255',
                Rule::unique('leads', 'email')->whereNull('deleted_at'),
                Rule::unique('customers', 'email')->whereNull('deleted_at'),
            ],
            'address'          => 'nullable|string',
            'lead_source_id'   => 'required|exists:lead_sources,id',
            'notes'            => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'phone.unique' => 'Nomor HP ini sudah terdaftar di sistem.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
        ];
    }
}
