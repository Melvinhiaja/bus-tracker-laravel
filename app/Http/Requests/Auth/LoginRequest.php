<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Menentukan apakah request ini diizinkan.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk login.
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'], // Ganti dari 'email' ke 'username'
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Proses autentikasi user.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Coba login menggunakan username dan password
        if (! Auth::attempt($this->only('username', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => trans('auth.failed'), // Pesan error jika gagal login
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }
//buat jika ingin password nya keliahtan 
//     public function authenticate(): void
// {
//     $this->ensureIsNotRateLimited();

//     $user = \App\Models\User::where('username', $this->username)->first();

//     if (!$user || $user->password !== $this->password) {  // Cek langsung password plain text
//         throw ValidationException::withMessages([
//             'username' => trans('auth.failed'),
//         ]);
//     }

//     auth()->login($user);  // Login manual jika cocok
//     RateLimiter::clear($this->throttleKey());
// }

    /**
     * Pastikan user tidak melebihi batas percobaan login.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Menghasilkan kunci throttle untuk rate limiting.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('username')).'|'.$this->ip());
    }
}
