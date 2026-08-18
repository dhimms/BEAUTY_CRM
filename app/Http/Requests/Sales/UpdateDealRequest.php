<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDealRequest extends FormRequest
{
    //ini ada di halaman deal, muncul pas klik tombol edit deal
    //memastikan user yang login boleh mengakses halaman ini
    public function authorize(): bool
    {
        return true;
    }

    //aturan validasi untuk inputan user
    public function rules(): array
    {
        return [
            'name'                => 'required|string|max:255',//memastikan nama deal diisi dan tidak lebih dari 255 karakter
            'expected_close_date' => 'nullable|date',//memastikan tanggal close deal diisi dan tidak kurang dari tanggal hari ini
        ];
    }

    //pesan error yang akan ditampilkan
    public function messages(): array
    {
        return [
            'name.required'  => 'Nama deal wajib diisi.',//pesan error jika nama deal tidak diisi
        ];
    }
}
