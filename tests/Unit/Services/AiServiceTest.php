<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\Contracts\AiProviderInterface;
use App\Services\FallbackAiService;
use Illuminate\Support\Facades\Http;

class AiServiceTest extends TestCase
{
    public function test_it_resolves_fallback_service()
    {
        $ai = app(AiProviderInterface::class);
        $this->assertInstanceOf(FallbackAiService::class, $ai);
    }
}

