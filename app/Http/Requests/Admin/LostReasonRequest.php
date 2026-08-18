<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LostReasonRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    // method rules berfungsi untuk mendefinisikan aturan validasi untuk setiap kolom
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
