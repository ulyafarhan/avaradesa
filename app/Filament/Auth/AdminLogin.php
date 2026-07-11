<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Login;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Validation\ValidationException;

/**
 * Halaman kustom login administrator desa pada panel Filament.
 *
 * Kelas ini menimpa halaman login bawaan Filament dengan mengganti input email
 * menjadi input username, serta menyesuaikan logika autentikasi dan pesan error
 * sesuai kebutuhan aplikasi desa.
 */
class AdminLogin extends Login
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
            ]);
    }

    /**
     * Menimpa komponen input email bawaan Filament menjadi input username.
     *
     * Perubahan ini memungkinkan administrator login menggunakan username
     * daripada alamat email, sesuai dengan skema autentikasi yang digunakan
     * pada model Administrator.
     *
     * @return Component Komponen input username dengan atribut yang telah ditentukan.
     */
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('Username')
            ->required()
            ->autocomplete('username')
            ->autofocus();
    }

    /**
     * Menyusun kredensial autentikasi dari data formulir yang dikirimkan.
     *
     * Memetakan field 'username' dan 'password' dari array data formulir
     * ke array kredensial yang akan digunakan oleh guard autentikasi Filament.
     *
     * @param  array  $data Data mentah dari formulir login.
     * @return array  Array kredensial berisi 'username' dan 'password'.
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password' => $data['password'],
        ];
    }

    /**
     * Melempar exception validasi dengan pesan error khusus saat login gagal.
     *
     * Pesan error ditampilkan dalam Bahasa Indonesia pada field username,
     * menggantikan pesan bawaan Filament yang berbahasa Inggris.
     *
     * @return never
     *
     * @throws ValidationException Selalu dilemparkan dengan pesan error yang telah ditentukan.
     */
    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.username' => 'Username atau password salah.',
        ]);
    }
}