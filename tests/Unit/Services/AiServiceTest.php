<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\Contracts\AiProviderInterface;
use App\Services\AiProviders\GeminiProvider;
use App\Services\AiProviders\OpenAiProvider;
use Illuminate\Support\Facades\Http;

class AiServiceTest extends TestCase
{
    public function test_it_resolves_fallback_service()
    {
        $ai = app(AiProviderInterface::class);
        $this->assertInstanceOf(\App\Services\FallbackAiService::class, $ai);
    }

    public function test_fallback_service_falls_back_when_first_provider_fails()
    {
        \App\Models\PengaturanDesa::set('ai_providers_list', [
            [
                'name' => 'Provider 1 (Invalid)',
                'provider_type' => 'openai',
                'api_key' => 'invalid-key-1',
                'model' => 'gpt-4o-mini',
                'base_url' => 'https://api.openai.com/v1',
                'priority' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Provider 2 (Valid)',
                'provider_type' => 'openai',
                'api_key' => 'valid-key-2',
                'model' => 'gpt-4o-mini',
                'base_url' => 'https://api.openai.com/v1',
                'priority' => 2,
                'is_active' => true,
            ]
        ], 'json');

        Http::fake([
            'api.openai.com/*' => function (\Illuminate\Http\Client\Request $request) {
                if ($request->hasHeader('Authorization', 'Bearer invalid-key-1')) {
                    return Http::response(['error' => 'Invalid API Key'], 401);
                }
                if ($request->hasHeader('Authorization', 'Bearer valid-key-2')) {
                    return Http::response([
                        'choices' => [
                            [
                                'message' => [
                                    'content' => '{"meta_description": "Deskripsi meta fallback.", "kata_kunci": "desa, desa"}'
                                ]
                            ]
                        ]
                    ], 200);
                }
                return Http::response([], 404);
            }
        ]);

        $ai = app(AiProviderInterface::class);
        $result = $ai->generateSeoMetadata('Judul Artikel', 'Konten.');

        $this->assertIsArray($result);
        $this->assertEquals('Deskripsi meta fallback.', $result['meta_description']);
    }

    public function test_gemini_provider_can_generate_seo_metadata_successfully()
    {
        config(['services.gemini.api_key' => 'test-key']);
        config(['services.gemini.model' => 'gemini-pro']);
        
        $provider = new GeminiProvider();

        $responsePayload = [
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => '{"meta_description": "Deskripsi meta untuk artikel desa.", "kata_kunci": "desa, desa, pembangunan"}']
                        ]
                    ]
                ]
            ]
        ];

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response($responsePayload, 200)
        ]);

        $result = $provider->generateSeoMetadata('Judul Artikel', 'Konten artikel panjang.');

        $this->assertIsArray($result);
        $this->assertEquals('Deskripsi meta untuk artikel desa.', $result['meta_description']);
        $this->assertEquals('desa, desa, pembangunan', $result['kata_kunci']);
    }

    public function test_openai_provider_can_generate_seo_metadata_successfully()
    {
        config(['services.ai.openai.api_key' => 'test-key']);
        config(['services.ai.openai.model' => 'gpt-4o-mini']);
        config(['services.ai.openai.base_url' => 'https://api.openai.com/v1']);
        
        $provider = new OpenAiProvider();

        $responsePayload = [
            'choices' => [
                [
                    'message' => [
                        'content' => '{"meta_description": "Deskripsi meta OpenAI.", "kata_kunci": "desa, desa"}'
                    ]
                ]
            ]
        ];

        Http::fake([
            'api.openai.com/*' => Http::response($responsePayload, 200)
        ]);

        $result = $provider->generateSeoMetadata('Judul Artikel', 'Konten artikel.');

        $this->assertIsArray($result);
        $this->assertEquals('Deskripsi meta OpenAI.', $result['meta_description']);
        $this->assertEquals('desa, desa', $result['kata_kunci']);
    }

    public function test_openai_provider_generates_copywriting_when_empty()
    {
        config(['services.ai.openai.api_key' => 'test-key']);
        config(['services.ai.openai.base_url' => 'https://api.openai.com/v1']);
        $provider = new OpenAiProvider();

        $responsePayload = [
            'choices' => [
                [
                    'message' => [
                        'content' => '<p>Ini artikel hasil generate dari judul.</p>'
                    ]
                ]
            ]
        ];

        Http::fake([
            'api.openai.com/*' => Http::response($responsePayload, 200)
        ]);

        $result = $provider->fixCopywriting('', 'Judul Baru Desa');

        $this->assertEquals('<p>Ini artikel hasil generate dari judul.</p>', $result);
    }

    public function test_openai_provider_fixes_copywriting_when_not_empty()
    {
        config(['services.ai.openai.api_key' => 'test-key']);
        config(['services.ai.openai.base_url' => 'https://api.openai.com/v1']);
        $provider = new OpenAiProvider();

        $responsePayload = [
            'choices' => [
                [
                    'message' => [
                        'content' => '<p>Hasil perbaikan teks asli.</p>'
                    ]
                ]
            ]
        ];

        Http::fake([
            'api.openai.com/*' => Http::response($responsePayload, 200)
        ]);

        $result = $provider->fixCopywriting('<p>Teks asli yang salah ejaan.</p>', 'Judul');

        $this->assertEquals('<p>Hasil perbaikan teks asli.</p>', $result);
    }
}

