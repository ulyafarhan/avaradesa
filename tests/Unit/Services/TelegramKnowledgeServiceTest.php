<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\TelegramKnowledgeService;
use App\Models\BotKnowledge;

class TelegramKnowledgeServiceTest extends TestCase
{
    protected TelegramKnowledgeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TelegramKnowledgeService();
    }

    public function test_it_can_match_greetings_correctly()
    {
        $response = $this->service->findStaticAnswer('Halo');
        $this->assertNotNull($response);
        $this->assertStringContainsString('Selamat datang di Bot AvaraDesa', $response);

        $response2 = $this->service->findStaticAnswer('assalamualaikum');
        $this->assertNotNull($response2);
        
        $response3 = $this->service->findStaticAnswer('p');
        $this->assertNotNull($response3);
    }

    public function test_it_can_match_sktm_keywords()
    {
        $response = $this->service->findStaticAnswer('bagaimana cara buat sktm?');
        $this->assertNotNull($response);
        $this->assertStringContainsString('Surat Keterangan Tidak Mampu', $response);
        $this->assertStringContainsString('KK', $response);
    }

    public function test_it_can_match_domisili_keywords()
    {
        $response = $this->service->findStaticAnswer('butuh surat domisili');
        $this->assertNotNull($response);
        $this->assertStringContainsString('Surat Keterangan Domisili', $response);
    }

    public function test_it_returns_null_for_unknown_questions()
    {
        $response = $this->service->findStaticAnswer('siapa nama presiden indonesia saat ini?');
        $this->assertNull($response);
    }

    public function test_it_can_retrieve_context_for_rag()
    {
        $context = $this->service->retrieveContext('profil desa');
        $this->assertStringContainsString('AvaraDesa', $context);
        
        $contextSurat = $this->service->retrieveContext('bagaimana cara mengambil surat?');
        $this->assertStringContainsString('QR Code', $contextSurat);
    }

    public function test_it_can_match_faq_from_database()
    {
        BotKnowledge::create([
            'kunci' => 'test_faq_db',
            'tipe' => 'faq',
            'pertanyaan_atau_topik' => 'Test FAQ Database',
            'kata_kunci' => ['kunci_rahasia_test', 'test_db'],
            'jawaban_atau_konten' => 'Jawaban dari database faq terverifikasi.',
            'is_aktif' => true,
        ]);

        $response = $this->service->findStaticAnswer('bagaimana kunci_rahasia_test bekerja?');
        $this->assertNotNull($response);
        $this->assertEquals('Jawaban dari database faq terverifikasi.', $response);

        BotKnowledge::where('kunci', 'test_faq_db')->delete();
    }

    public function test_it_can_retrieve_context_from_database_for_rag()
    {
        BotKnowledge::create([
            'kunci' => 'test_kb_db',
            'tipe' => 'kb',
            'pertanyaan_atau_topik' => 'Test KB Database',
            'kata_kunci' => ['kb_rahasia_test', 'test_kb'],
            'jawaban_atau_konten' => 'Informasi penting dari database KB untuk AI provider.',
            'is_aktif' => true,
        ]);

        $context = $this->service->retrieveContext('tolong jelaskan kb_rahasia_test');
        $this->assertStringContainsString('Informasi penting dari database KB', $context);

        BotKnowledge::where('kunci', 'test_kb_db')->delete();
    }
}
