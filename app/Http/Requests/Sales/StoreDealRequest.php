<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class StoreDealRequest extends FormRequest
{
    //ini ada di halaman lead, muncul pas klik tombol create deal
    //memastikan user yang login boleh mengakses halaman ini
    public function authorize(): bool
    {
        return true;
    }

    //aturan validasi untuk inputan user
    public function rules(): array
    {
        return [
            'lead_id'             => 'required|exists:leads,id',//memastikan lead yang dipilih ada di database
            'name'                => 'required|string|max:255',//memastikan nama deal diisi dan tidak lebih dari 255 karakter
            'pipeline_stage_id'   => 'nullable|exists:pipeline_stages,id',//memastikan pipeline stage yang dipilih ada di database
            'expected_close_date' => 'nullable|date|after_or_equal:today',//memastikan tanggal close deal diisi dan tidak kurang dari tanggal hari ini
        ];
    }

    //pesan error yang akan ditampilkan
    public function messages(): array
    {
        return [
            'lead_id.required'  => 'Lead wajib dipilih.',//pesan error jika lead tidak diisi
            'lead_id.exists'    => 'Lead tidak ditemukan.',//pesan error jika lead tidak ditemukan
            'name.required'     => 'Nama deal wajib diisi.',//pesan error jika nama deal tidak diisi
            'expected_close_date.after_or_equal' => 'Expected close date harus hari ini atau setelahnya.',//pesan error jika tanggal close deal tidak kurang dari tanggal hari ini
        ];
    }
}
