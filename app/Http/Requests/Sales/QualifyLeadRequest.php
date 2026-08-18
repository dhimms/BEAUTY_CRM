<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class QualifyLeadRequest extends FormRequest
{
    //ini ada di halaman lead, trus muncul pas kita klik tombol qualified leed
    //tombol qualified biar bisa di klik dan mencegah manipulation data
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'qualification' => 'required|in:qualified,unqualified,not_fit',//memastikan qualification yang dipilih ada di database
            'notes'         => 'nullable|string|max:1000',//memastikan notes diisi dan tidak lebih dari 1000 karakter
        ];
    }

    public function messages(): array
    {
        return [
            'qualification.required' => 'Qualification wajib dipilih.',//pesan error jika qualification tidak diisi
            'qualification.in'       => 'Qualification tidak valid.',//pesan error jika qualification tidak valid
        ];
    }
}
