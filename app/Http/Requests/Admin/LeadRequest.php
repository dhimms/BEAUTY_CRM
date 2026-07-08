<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['nullable', 'email', 'max:255'],
            'phone'          => ['required', 'string', 'max:20'],
            'address'        => ['nullable', 'string'],
            'lead_source_id' => ['required', 'exists:lead_sources,id'],
            'assigned_to'    => ['nullable', 'exists:users,id'],
            'status'         => ['required', Rule::in(['new', 'contacted', 'qualified', 'converted', 'closed'])],
            'qualification'  => ['nullable', Rule::in(['qualified', 'unqualified', 'not_fit'])],
            'notes'          => ['nullable', 'string'],
        ];
    }
}
