<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Notifications\Notifiable;

/**
 * Model untuk akun pengguna administrator sistem (Laravel default).
 *
 * Tabel: users
 * Menggunakan ULID sebagai primary key dan HasFactory untuk pembuatan data dummy.
 * Model ini merupakan model autentikasi bawaan Laravel, terpisah dari
 * model Administrator yang digunakan di panel Filament.
 *
 * @property  string  $id  ULID unik pengguna
 * @property  string  $name  Nama lengkap pengguna
 * @property  string  $email  Alamat email (unique)
 * @property  \Carbon\Carbon|null  $email_verified_at  Waktu verifikasi email
 * @property  string  $password  Hashed password
 * @property  string|null  $remember_token  Token "remember me"
 * @property  \Carbon\Carbon|null  $created_at  Waktu pembuatan akun
 * @property  \Carbon\Carbon|null  $updated_at  Waktu pembaruan akun
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUlids;

    /**
     * Casting atribut ke tipe data native PHP.
     *
     * @return  array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
