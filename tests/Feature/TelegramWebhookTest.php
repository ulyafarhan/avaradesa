<?php

namespace Tests\Feature;

use App\Models\Penduduk;
use App\Models\Keluarga;
use App\Models\BotKnowledge;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class TelegramWebhookTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_handles_start_command_correctly()
    {
        $this->instance(TelegramService::class, Mockery::mock(TelegramService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->once();
            $mock->shouldReceive('setWebhook')->andReturn(true);
        }));

        $response = $this->postJson('/api/v1/telegram/webhook', [
            'message' => [
                'text' => '/start',
                'chat' => ['id' => 12345],
                'from' => ['id' => 67890, 'first_name' => 'Test'],
            ],
        ]);

        $response->assertStatus(200);
    }

    public function test_it_handles_bind_command_correctly()
    {
        $keluarga = Keluarga::factory()->create();
        $warga = Penduduk::factory()->create([
            'no_kk' => $keluarga->no_kk,
        ]);

        $this->instance(TelegramService::class, Mockery::mock(TelegramService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->once();
            $mock->shouldReceive('setWebhook')->andReturn(true);
        }));

        $response = $this->postJson('/api/v1/telegram/webhook', [
            'message' => [
                'text' => '/bind ' . $warga->nik,
                'chat' => ['id' => 12345],
                'from' => ['id' => 67890, 'first_name' => 'Test'],
            ],
        ]);

        $response->assertStatus(200);
    }

    public function test_it_returns_empty_for_unknown_commands()
    {
        $this->instance(TelegramService::class, Mockery::mock(TelegramService::class, function ($mock) {
            $mock->shouldReceive('setWebhook')->andReturn(true);
        }));

        $response = $this->postJson('/api/v1/telegram/webhook', [
            'message' => [
                'text' => '/unknown_command',
                'chat' => ['id' => 12345],
                'from' => ['id' => 67890, 'first_name' => 'Test'],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJson(['ok' => true]);
    }

    public function test_it_matches_local_knowledge_base()
    {
        BotKnowledge::create([
            'kunci' => 'faq-sktm',
            'tipe' => 'faq',
            'pertanyaan_atau_topik' => 'Cara buat SKTM',
            'kata_kunci' => ['sktm'],
            'jawaban_atau_konten' => 'Untuk membuat SKTM, siapkan KTP dan KK.',
            'is_aktif' => true,
        ]);

        $this->instance(TelegramService::class, Mockery::mock(TelegramService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->once();
            $mock->shouldReceive('setWebhook')->andReturn(true);
        }));

        $response = $this->postJson('/api/v1/telegram/webhook', [
            'message' => [
                'text' => 'bagaimana cara buat sktm?',
                'chat' => ['id' => 12345],
                'from' => ['id' => 67890, 'first_name' => 'Test'],
            ],
        ]);

        $response->assertStatus(200);
    }

    public function test_it_enforces_rate_limits_for_ai_questions()
    {
        $this->instance(TelegramService::class, Mockery::mock(TelegramService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->times(6);
            $mock->shouldReceive('setWebhook')->andReturn(true);
        }));

        for ($i = 0; $i < 15; $i++) {
            $this->postJson('/api/v1/telegram/webhook', [
                'message' => [
                    'text' => 'pertanyaan random ' . $i,
                    'chat' => ['id' => 99999],
                    'from' => ['id' => 99999, 'first_name' => 'Spammer'],
                ],
            ]);
        }

        $response = $this->postJson('/api/v1/telegram/webhook', [
            'message' => [
                'text' => 'pertanyaan ke-16',
                'chat' => ['id' => 99999],
                'from' => ['id' => 99999, 'first_name' => 'Spammer'],
            ],
        ]);

        $response->assertStatus(200);
    }

    public function test_webhook_secret_token_authentication_fails()
    {
        config(['services.telegram.webhook_secret' => 'secret-token-123']);

        $response = $this->withHeaders([
            'X-Telegram-Bot-Api-Secret-Token' => 'wrong-token',
        ])->postJson('/api/v1/telegram/webhook', [
            'message' => [
                'text' => '/start',
                'chat' => ['id' => 12345],
            ],
        ]);

        $response->assertStatus(403)
            ->assertJson(['error' => 'Unauthorized']);
    }

    public function test_callback_query_processing()
    {
        $this->instance(TelegramService::class, Mockery::mock(TelegramService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')->with(12345, 'Callback received: test_data')->once();
        }));

        $response = $this->postJson('/api/v1/telegram/webhook', [
            'callback_query' => [
                'data' => 'test_data',
                'message' => [
                    'chat' => ['id' => 12345],
                ],
            ],
        ]);

        $response->assertStatus(200)
            ->assertJson(['ok' => true]);
    }

    public function test_bind_command_without_nik_parameter()
    {
        $this->instance(TelegramService::class, Mockery::mock(TelegramService::class, function ($mock) {
            $mock->shouldReceive('sendMessage')
                ->with(12345, "Format: /bind [NIK]\nContoh: /bind 1234567890123456")
                ->once();
        }));

        $response = $this->postJson('/api/v1/telegram/webhook', [
            'message' => [
                'text' => '/bind',
                'chat' => ['id' => 12345],
            ],
        ]);

        $response->assertStatus(200);
    }

    public function test_empty_message_ignored()
    {
        $this->instance(TelegramService::class, Mockery::mock(TelegramService::class, function ($mock) {
            $mock->shouldNotReceive('sendMessage');
        }));

        $response = $this->postJson('/api/v1/telegram/webhook', [
            'message' => [
                'text' => '   ',
                'chat' => ['id' => 12345],
            ],
        ]);

        $response->assertStatus(200);
    }
}
