<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class CloseDealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'outcome'        => 'required|in:won,lost',
            'lost_reason_id' => 'required_if:outcome,lost|nullable|exists:lost_reasons,id',
            'lost_notes'     => 'required_if:outcome,lost|nullable|string|min:10|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'outcome.required'          => 'Outcome wajib dipilih.',
            'outcome.in'                => 'Outcome harus won atau lost.',
            'lost_reason_id.required_if' => 'Alasan lost wajib dipilih.',
            'lost_reason_id.exists'     => 'Alasan lost tidak valid.',
            'lost_notes.required_if'    => 'Catatan lost wajib diisi.',
        ];
    }
}
