<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
{
    //ini ada di halaman lead dan deal, muncul pas klik tombol activity
    //memastikan user yang login boleh mengakses halaman ini
    public function authorize(): bool
    {
        return true;
    }

    //aturan validasi untuk inputan user
    public function rules(): array
    {
        return [
            'activitable_type' => 'required|in:lead,deal',//memastikan activitable type yang dipilih ada di database
            'activitable_id'   => 'required|integer',//memastikan activitable id yang dipilih ada di database
            'type'             => 'required|in:call,whatsapp,email,meeting,note,other',//memastikan type yang dipilih ada di database
            'subject'          => 'nullable|string|max:255',//memastikan subject diisi dan tidak lebih dari 255 karakter
            'description'      => 'nullable|string|max:2000',//memastikan description diisi dan tidak lebih dari 2000 karakter
            'duration'         => 'nullable|string|max:20',//memastikan duration diisi dan tidak lebih dari 20 karakter
            'result'           => 'nullable|string|max:100',//memastikan result diisi dan tidak lebih dari 100 karakter
            'activity_date'    => 'nullable|date',//memastikan activity date diisi dan tidak kurang dari tanggal hari ini
            'follow_up_date'   => 'nullable|date|after_or_equal:today',//memastikan follow up date diisi dan tidak kurang dari tanggal hari ini
            'follow_up_type'   => 'nullable|in:call,whatsapp,email,meeting',//memastikan follow up type yang dipilih ada di database
            'follow_up_notes'  => 'nullable|string|max:1000',//memastikan follow up notes diisi dan tidak lebih dari 1000 karakter
        ];
    }

    /**
     * Map activitable_type to the full model class.
     */
    //memastikan activitable type yang dipilih ada di database
    public function getActivitableType(): string
    {
        return match ($this->activitable_type) {
            'lead' => \App\Models\Lead::class,
            'deal' => \App\Models\Deal::class,
        };
    }

    //pesan error yang akan ditampilkan
    public function messages(): array
    {
        return [
            'type.required'           => 'Tipe aktivitas wajib diisi.',//pesan error jika type tidak diisi
            'type.in'                 => 'Tipe aktivitas tidak valid.',//pesan error jika type tidak valid
            'activitable_type.required'=> 'Target aktivitas wajib diisi.',//pesan error jika activitable type tidak diisi
            'follow_up_date.after_or_equal' => 'Tanggal follow-up harus hari ini atau setelahnya.',//pesan error jika follow up date tidak kurang dari tanggal hari ini
        ];
    }
}
