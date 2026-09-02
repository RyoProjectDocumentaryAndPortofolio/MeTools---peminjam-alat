<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Mengambil ID user dari route (biasanya berupa parameter {user})
        $user = $this->route('user');

        // Memastikan kita mendapatkan ID (mengantisipasi jika parameter route berupa objek Model)
        $userId = $user instanceof \App\Models\User ? $user->id : $user;

        return [
            'name' => ['required', 'string', 'max:255'],
            
            // Mengabaikan ID user yang sedang di-update agar tidak terjadi error "Email sudah terdaftar"
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            
            // Password bisa nullable karena biasanya saat update, password hanya diisi jika ingin mengganti
            'password' => ['nullable', 'string', Password::min(8)->letters()->numbers()],
            
            'role' => ['required', Rule::in(['admin', 'petugas', 'peminjam'])],
            'no_hp' => ['nullable', 'string', 'max:15'],
            'alamat' => ['nullable', 'string'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }
}