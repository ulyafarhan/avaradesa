<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\ChatbotLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatbotLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_chatbot_log(): void
    {
        $log = ChatbotLog::create([
            'telegram_chat_id' => '123456789',
            'pesan_masuk' => 'Halo min',
            'balasan_ai' => 'Halo, ada yang bisa dibantu?',
            'tokens_used' => 150,
        ]);

        $this->assertDatabaseHas('chatbot_logs', [
            'id' => $log->id,
            'telegram_chat_id' => '123456789',
            'pesan_masuk' => 'Halo min',
            'balasan_ai' => 'Halo, ada yang bisa dibantu?',
            'tokens_used' => 150,
        ]);
    }

    public function test_chatbot_log_has_timestamps(): void
    {
        $log = ChatbotLog::create([
            'telegram_chat_id' => '987654321',
            'pesan_masuk' => 'Tanya jadwal',
            'balasan_ai' => 'Jadwal hari ini...',
            'tokens_used' => 100,
        ]);

        $this->assertNotNull($log->created_at);
        $this->assertDatabaseHas('chatbot_logs', [
            'id' => $log->id,
        ]);
        
        // Ensure created_at was saved to database
        $freshLog = ChatbotLog::find($log->id);
        $this->assertNotNull($freshLog->created_at);
    }
}
