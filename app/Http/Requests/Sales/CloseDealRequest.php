<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class CloseDealRequest extends FormRequest
{
    //ini letaknya ada di halaman deal, bukan di lead, trus dia muncul pas kita klik tombol close deal
    //memastikan user yang login boleh mengakses halaman ini
    public function authorize(): bool
    {
        return true;
    }

    //aturan validasi untuk inputan user
    public function rules(): array
    {
        return [
            'outcome'        => 'required|in:won,lost',//memastikan outcome yang dipilih ada di database
            'lost_reason_id' => 'required_if:outcome,lost|nullable|exists:lost_reasons,id',//memastikan alasan lost yang dipilih ada di database
            'lost_notes'     => 'required_if:outcome,lost|nullable|string|min:10|max:1000',//memastikan catatan lost diisi dan tidak lebih dari 1000 karakter
        ];
    }

    //pesan error yang akan ditampilkan
    public function messages(): array
    {
        return [
            'outcome.required'          => 'Outcome wajib dipilih.',//pesan error jika outcome tidak diisi
            'outcome.in'                => 'Outcome harus won atau lost.',//pesan error jika outcome tidak valid
            'lost_reason_id.required_if' => 'Alasan lost wajib dipilih.',//pesan error jika alasan lost tidak diisi
            'lost_reason_id.exists'     => 'Alasan lost tidak valid.',//pesan error jika alasan lost tidak valid
            'lost_notes.required_if'    => 'Catatan lost wajib diisi.',//pesan error jika catatan lost tidak diisi
        ];
    }
}
