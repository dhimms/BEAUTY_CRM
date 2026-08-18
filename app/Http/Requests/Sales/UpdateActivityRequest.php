<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityRequest extends FormRequest
{
    //CATATAN : INI BELUM BERFUNGSI KARNA BELUM ADA DI TAMPILAN
    //ini ada di halaman lead dan deal, muncul pas klik tombol edit activity
    //memastikan user yang login boleh mengakses halaman ini
    public function authorize(): bool
    {
        return true;
    }

    //aturan validasi untuk inputan user
    public function rules(): array
    {
        return [
            'type'            => 'required|in:call,whatsapp,email,meeting,note,other',//memastikan type yang dipilih ada di database
            'subject'         => 'nullable|string|max:255',//memastikan subject diisi dan tidak lebih dari 255 karakter
            'description'     => 'nullable|string|max:2000',//memastikan description diisi dan tidak lebih dari 2000 karakter
            'duration'        => 'nullable|string|max:20',//memastikan duration diisi dan tidak lebih dari 20 karakter
            'result'          => 'nullable|string|max:100',//memastikan result diisi dan tidak lebih dari 100 karakter
            'activity_date'   => 'nullable|date',//memastikan activity date diisi dan tidak kurang dari tanggal hari ini
            'follow_up_date'  => 'nullable|date',//memastikan follow up date diisi dan tidak kurang dari tanggal hari ini
            'follow_up_type'  => 'nullable|in:call,whatsapp,email,meeting',//memastikan follow up type yang dipilih ada di database
            'follow_up_notes' => 'nullable|string|max:1000',//memastikan follow up notes diisi dan tidak lebih dari 1000 karakter
        ];
    }

    //pesan error yang akan ditampilkan
    public function messages(): array
    {
        return [
            'type.required' => 'Tipe aktivitas wajib diisi.',//pesan error jika type tidak diisi
            'type.in'       => 'Tipe aktivitas tidak valid.',//pesan error jika type tidak valid
        ];
    }
}
