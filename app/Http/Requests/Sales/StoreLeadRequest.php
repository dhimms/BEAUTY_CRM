<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadRequest extends FormRequest
{
    //ini ada di halaman lead, muncul pas klik tombol create lead
    //memastikan user yang login boleh mengakses halaman ini
    public function authorize()
    {
        return true;
    }

    //aturan validasi untuk inputan user
    public function rules()
    {
        return [
            'name'             => 'required|string|max:255',//memastikan nama diisi dan tidak lebih dari 255 karakter
            'phone'            => ['required','string','max:20',//memastikan nomor hp diisi dan tidak lebih dari 20 karakter
                Rule::unique('leads', 'phone')->whereNull('deleted_at'),//memastikan nomor hp tidak ada di database
                Rule::unique('customers', 'phone')->whereNull('deleted_at'),//memastikan nomor hp tidak ada di database
            ],
            'email'            => ['required','email','max:255',//memastikan email diisi dan tidak lebih dari 255 karakter
                Rule::unique('leads', 'email')->whereNull('deleted_at'),//memastikan email tidak ada di database
                Rule::unique('customers', 'email')->whereNull('deleted_at'),//memastikan email tidak ada di database
            ],
            'address'          => 'nullable|string',//memastikan alamat diisi dan tidak lebih dari 255 karakter
            'lead_source_id'   => 'required|exists:lead_sources,id',//memastikan lead source yang dipilih ada di database
            'notes'            => 'nullable|string',//memastikan catatan diisi dan tidak lebih dari 255 karakter
        ];
    }
    //pesan error yang akan ditampilkan
    public function messages()
    {
        return [
            'phone.unique' => 'Nomor HP ini sudah terdaftar di sistem.',//pesan error jika nomor hp sudah terdaftar
            'email.unique' => 'Email ini sudah terdaftar di sistem.',//pesan error jika email sudah terdaftar
        ];
    }
}
