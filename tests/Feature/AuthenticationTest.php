<?php

namespace Tests\Feature;

use App\Models\Administrator;
use App\Models\Keluarga;
use App\Models\Penduduk;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function test_warga_can_login_with_nik_and_no_kk()
    {
        $keluarga = Keluarga::factory()->create();
        $penduduk = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);

        $response = $this->postJson('/api/v1/auth/login/warga', [
            'nik' => $penduduk->nik,
            'no_kk' => $keluarga->no_kk,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'user', 'token']);
    }

    public function test_warga_cannot_login_with_invalid_nik()
    {
        $response = $this->postJson('/api/v1/auth/login/warga', [
            'nik' => '9999999999999999',
            'no_kk' => '1234567890123456',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nik']);
    }

    public function test_warga_cannot_login_with_invalid_no_kk()
    {
        $keluarga = Keluarga::factory()->create();
        $penduduk = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);

        $response = $this->postJson('/api/v1/auth/login/warga', [
            'nik' => $penduduk->nik,
            'no_kk' => '9999999999999999',
        ]);

        $response->assertStatus(422);
    }

    public function test_warga_cannot_login_if_not_active()
    {
        $keluarga = Keluarga::factory()->create();
        $penduduk = Penduduk::factory()->pindah()->create(['no_kk' => $keluarga->no_kk]);

        $response = $this->postJson('/api/v1/auth/login/warga', [
            'nik' => $penduduk->nik,
            'no_kk' => $keluarga->no_kk,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nik']);
    }

    public function test_admin_can_login_with_credentials()
    {
        $admin = Administrator::factory()->operator()->create();

        $response = $this->postJson('/api/v1/auth/login/admin', [
            'username' => $admin->username,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'user', 'token']);
    }

    public function test_admin_cannot_login_with_wrong_password()
    {
        $admin = Administrator::factory()->operator()->create();

        $response = $this->postJson('/api/v1/auth/login/admin', [
            'username' => $admin->username,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username']);
    }

    public function test_admin_cannot_login_nonexistent_user()
    {
        $response = $this->postJson('/api/v1/auth/login/admin', [
            'username' => 'nonexistent',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
    }

    public function test_authenticated_user_can_get_profile()
    {
        $keluarga = Keluarga::factory()->create();
        $penduduk = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);

        $token = $penduduk->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->getJson('/api/v1/auth/profile');

        $response->assertStatus(200)
            ->assertJsonPath('user.nik', $penduduk->nik);
    }

    public function test_user_can_logout()
    {
        $keluarga = Keluarga::factory()->create();
        $penduduk = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);

        $token = $penduduk->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logout berhasil']);
    }

    public function test_token_invalid_after_logout()
    {
        $keluarga = Keluarga::factory()->create();
        $penduduk = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);

        $token = $penduduk->createToken('test', ['warga'])->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertStatus(200);

        $this->assertDatabaseEmpty('personal_access_tokens');
    }

    public function test_unauthenticated_cannot_access_profile()
    {
        $response = $this->getJson('/api/v1/auth/profile');

        $response->assertStatus(401);
    }

    public function test_warga_can_bind_telegram()
    {
        $keluarga = Keluarga::factory()->create();
        $penduduk = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);

        $token = $penduduk->createToken('test', ['warga'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/auth/bind-telegram', [
                'telegram_chat_id' => '123456789',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('penduduk', [
            'nik' => $penduduk->nik,
            'telegram_chat_id' => '123456789',
        ]);
    }

    public function test_warga_cannot_bind_duplicate_telegram()
    {
        $keluarga = Keluarga::factory()->create();
        $penduduk1 = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);
        $penduduk2 = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);

        $token1 = $penduduk1->createToken('test', ['warga'])->plainTextToken;
        $this->withToken($token1)->postJson('/api/v1/auth/bind-telegram', [
            'telegram_chat_id' => '123456789',
        ]);

        $token2 = $penduduk2->createToken('test', ['warga'])->plainTextToken;
        $response = $this->withToken($token2)
            ->postJson('/api/v1/auth/bind-telegram', [
                'telegram_chat_id' => '123456789',
            ]);

        $response->assertStatus(422);
    }

    public function test_admin_cannot_bind_telegram()
    {
        $admin = Administrator::factory()->operator()->create();

        $token = $admin->createToken('test', ['admin'])->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/auth/bind-telegram', [
                'telegram_chat_id' => '123456789',
            ]);

        $response->assertStatus(403);
    }
}
