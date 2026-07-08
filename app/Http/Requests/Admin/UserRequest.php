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

    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? $this->route('user');
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone'     => ['nullable', 'string', 'max:20'],
            'role'      => ['required', 'string', 'exists:roles,name'],
            'is_active' => ['boolean'],
            'avatar'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'password'  => $isUpdate
                ? ['nullable', 'confirmed', Password::min(8)]
                : ['required', 'confirmed', Password::min(8)],
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
