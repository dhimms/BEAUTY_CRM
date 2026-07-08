<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DealRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'                 => ['required', 'string', 'max:255'],
            'value'                => ['required', 'numeric', 'min:0'],
            'pipeline_stage_id'    => ['required', 'exists:pipeline_stages,id'],
            'status'               => ['required', Rule::in(['open', 'won', 'lost'])],
            'lost_reason_id'       => ['nullable', 'required_if:status,lost', 'exists:lost_reasons,id'],
            'lost_notes'           => ['nullable', 'string'],
            'expected_close_date'  => ['nullable', 'date'],
            'assigned_to'          => ['nullable', 'exists:users,id'],
        ];
    }
}
