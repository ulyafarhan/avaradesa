<?php

namespace Tests\Unit\Services;

use App\Models\Penduduk;
use App\Services\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TelegramServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.telegram.bot_token' => 'dummy_token']);
    }

    public function test_send_message_with_keyboard_and_markdown()
    {
        Http::fake([
            'https://api.telegram.org/botdummy_token/sendMessage' => Http::response(['ok' => true], 200),
        ]);

        $service = new TelegramService();
        $keyboard = ['inline_keyboard' => [[['text' => 'Btn', 'url' => 'http']]]];
        $result = $service->sendMessage('12345', '**Bold**', $keyboard);

        $this->assertTrue($result);
        
        Http::assertSent(function ($request) use ($keyboard) {
            return $request->url() == 'https://api.telegram.org/botdummy_token/sendMessage' &&
                   $request['chat_id'] == '12345' &&
                   $request['text'] == '<b>Bold</b>' &&
                   $request['parse_mode'] == 'HTML' &&
                   $request['reply_markup'] == json_encode($keyboard);
        });
    }

    public function test_send_photo()
    {
        Http::fake([
            'https://api.telegram.org/botdummy_token/sendPhoto' => Http::response(['ok' => true], 200),
        ]);

        $service = new TelegramService();
        $result = $service->sendPhoto('12345', 'http://url.com/img.jpg', 'Caption');

        $this->assertTrue($result);
        
        Http::assertSent(function ($request) {
            return $request->url() == 'https://api.telegram.org/botdummy_token/sendPhoto' &&
                   $request['chat_id'] == '12345' &&
                   $request['photo'] == 'http://url.com/img.jpg' &&
                   $request['caption'] == 'Caption';
        });
    }

    public function test_send_document()
    {
        Http::fake([
            'https://api.telegram.org/botdummy_token/sendDocument' => Http::response(['ok' => true], 200),
        ]);

        // Create a temporary file
        $filePath = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($filePath, 'dummy content');

        $service = new TelegramService();
        $result = $service->sendDocument('12345', $filePath, 'Doc Caption');

        $this->assertTrue($result);
        
        Http::assertSent(function ($request) {
            return $request->url() == 'https://api.telegram.org/botdummy_token/sendDocument';
        });

        unlink($filePath);
    }

    public function test_broadcast_with_rate_limit_delay()
    {
        Http::fake([
            'https://api.telegram.org/botdummy_token/sendMessage' => Http::response(['ok' => true], 200),
        ]);

        $service = new TelegramService();
        
        $startTime = microtime(true);
        $result = $service->broadcast(['111', '222'], 'Hello');
        $endTime = microtime(true);
        
        $this->assertEquals(2, $result['success']);
        $this->assertEquals(0, $result['failed']);
        
        // 2 items * 50ms (50000us) = ~100ms
        $this->assertGreaterThanOrEqual(0.1, $endTime - $startTime);
    }

    public function test_notify_pengajuan_status_sends_formatted_text()
    {
        Http::fake([
            'https://api.telegram.org/botdummy_token/sendMessage' => Http::response(['ok' => true], 200),
        ]);

        $penduduk = Penduduk::factory()->create([
            'telegram_chat_id' => '98765'
        ]);

        $service = new TelegramService();
        $service->notifyPengajuanStatus($penduduk->nik, 'Disetujui', 'REG-001');
        
        Http::assertSent(function ($request) {
            return str_contains($request['text'] ?? '', 'REG-001') &&
                   $request['chat_id'] == '98765';
        });
    }

    public function test_notify_mutasi_status_sends_formatted_text()
    {
        Http::fake([
            'https://api.telegram.org/botdummy_token/sendMessage' => Http::response(['ok' => true], 200),
        ]);

        $penduduk = Penduduk::factory()->create([
            'telegram_chat_id' => '98765'
        ]);

        $service = new TelegramService();
        $service->notifyMutasiStatus($penduduk->nik, 'Kematian', 'Disetujui');
        
        Http::assertSent(function ($request) {
            return str_contains($request['text'], 'Kematian') &&
                   str_contains($request['text'], '[Disetujui]') &&
                   $request['chat_id'] == '98765';
        });
    }
}
