<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessTelegramMessageJob;
use App\Services\Contracts\AiProviderInterface;
use App\Services\TelegramKnowledgeService;
use App\Services\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Mockery\MockInterface;

class ProcessTelegramMessageJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_stores_cache_and_sends_telegram_reply()
    {
        $chatId = '123456';
        $text = 'Halo';
        $aiResponse = 'Halo juga';
        $context = 'Sample Context';

        $knowledge = $this->mock(TelegramKnowledgeService::class, function (MockInterface $mock) use ($text, $context) {
            $mock->shouldReceive('retrieveContext')
                 ->once()
                 ->with($text)
                 ->andReturn($context);
        });

        $ai = $this->mock(AiProviderInterface::class, function (MockInterface $mock) use ($text, $chatId, $context, $aiResponse) {
            $mock->shouldReceive('generateResponse')
                 ->once()
                 ->with($text, $chatId, $context)
                 ->andReturn($aiResponse);
        });

        $telegram = $this->mock(TelegramService::class, function (MockInterface $mock) use ($chatId, $aiResponse) {
            $mock->shouldReceive('sendMessage')
                 ->once()
                 ->with($chatId, $aiResponse);
        });

        $job = new ProcessTelegramMessageJob($chatId, $text);
        $job->handle($ai, $telegram, $knowledge);

        $cacheKey = 'telegram_reply_' . md5(trim(strtolower($text)));
        $this->assertEquals($aiResponse, Cache::get($cacheKey));
    }
}
