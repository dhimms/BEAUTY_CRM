<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    } 

    // ini adalah method untuk melakukan validasi saat menambah atau mengedit user
    public function rules(): array
    {   // ini adalah sebuah variabel untuk mendapatkan id user yang akan di edit atau di tambah
        $userId = $this->route('user')?->id ?? $this->route('user');
        // ini adalah sebuah variabel untuk mengecek apakah user sedang diedit atau ditambah
        // isMethod() adalah method bawaan dari FormRequest yang digunakan untuk mengecek apakah request adalah method put atau patch
        // jadi jika user sedang diedit maka password tidak akan dicek
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
   
        return [
            'name'      => ['required', 'string', 'max:255'],
            // pada tahap menambah user, email tidak boleh ada yang sama
            // variabel $userId disini berfungsi untuk membedakan apakah user sedang diedit atau ditambah
            // jika user sedang diedit maka email tidak akan dicek
            // RULE::UNIQUE() adalah method yang digunakan untuk mengecek apakah data sudah ada di database
            'email'     => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone'     => ['nullable', 'string', 'max:20'],
            'role'      => ['required', 'string', 'exists:roles,name'],
            'is_active' => ['boolean'],
            'monthly_target' => ['nullable', 'integer', 'min:1'],
            'avatar'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'password'  => $isUpdate
                ? ['nullable', 'confirmed', Password::min(8)]  // edit boleh kosong
                : ['required', 'confirmed', Password::min(8)], // tambah harus di isi 
        ];
    }

    public function messages(): array
    {
        return [
            'role.exists'  => 'Role yang dipilih tidak valid.',
            'avatar.max'   => 'Ukuran avatar maksimal 2MB.',
            'avatar.image' => 'Avatar harus berupa file gambar.',
            'email.unique' => 'Email sudah digunakan oleh user lain.',
        ];
    }
}
