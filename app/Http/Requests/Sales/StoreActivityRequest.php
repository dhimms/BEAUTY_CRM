<?php

namespace App\Http\Requests\Sales;

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
            'activitable_type' => 'required|in:lead,deal',
            'activitable_id'   => 'required|integer',
            'type'             => 'required|in:call,whatsapp,email,meeting,note,other',
            'subject'          => 'nullable|string|max:255',
            'description'      => 'nullable|string|max:2000',
            'duration'         => 'nullable|string|max:20',
            'result'           => 'nullable|string|max:100',
            'activity_date'    => 'nullable|date',
            'follow_up_date'   => 'nullable|date|after_or_equal:today',
            'follow_up_type'   => 'nullable|in:call,whatsapp,email,meeting',
            'follow_up_notes'  => 'nullable|string|max:1000',
        ];
    }

    /**
     * Map activitable_type to the full model class.
     */
    public function getActivitableType(): string
    {
        return match ($this->activitable_type) {
            'lead' => \App\Models\Lead::class,
            'deal' => \App\Models\Deal::class,
        };
    }

    public function messages(): array
    {
        return [
            'type.required'           => 'Tipe aktivitas wajib diisi.',
            'type.in'                 => 'Tipe aktivitas tidak valid.',
            'activitable_type.required'=> 'Target aktivitas wajib diisi.',
            'follow_up_date.after_or_equal' => 'Tanggal follow-up harus hari ini atau setelahnya.',
        ];
    }
}
