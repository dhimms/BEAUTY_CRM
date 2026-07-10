<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

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
            'phone'            => 'required|string|max:20',
            'email'            => 'nullable|email|max:255',
            'address'          => 'nullable|string',
            'lead_source_id'   => 'nullable|exists:lead_sources,id',
            'notes'            => 'nullable|string',
        ];
    }
}
