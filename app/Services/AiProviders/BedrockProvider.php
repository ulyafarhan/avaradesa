<?php

namespace App\Services\AiProviders;

use App\Services\Contracts\AiProviderInterface;
use App\Models\ChatbotLog;
use Aws\BedrockRuntime\BedrockRuntimeClient;
use Illuminate\Support\Facades\Log;

class BedrockProvider extends BaseAiProvider implements AiProviderInterface
{
    protected string $model;
    protected BedrockRuntimeClient $client;

    public function __construct()
    {
        $accessKey = config('services.ai.bedrock.access_key', '');
        $secretKey = config('services.ai.bedrock.secret_key', '');
        $region = config('services.ai.bedrock.region', 'us-east-1');
        $this->model = config('services.ai.bedrock.model', 'anthropic.claude-v2');

        $this->client = new BedrockRuntimeClient([
            'region' => $region,
            'version' => 'latest',
            'credentials' => [
                'key' => $accessKey,
                'secret' => $secretKey,
            ],
        ]);
    }

    protected function invokeModel(string $prompt, int $maxTokens = 1024, float $temperature = 0.5): ?string
    {
        $modelId = $this->model;

        $body = [
            'anthropic_version' => 'bedrock-2023-05-31',
            'max_tokens' => $maxTokens,
            'temperature' => $temperature,
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ];

        try {
            $result = $this->client->invokeModel([
                'modelId' => $modelId,
                'contentType' => 'application/json',
                'body' => json_encode($body),
            ]);

            $responseBody = $result['body']->getContents();
            $decoded = json_decode($responseBody, true);

            return $decoded['content'][0]['text'] ?? null;
        } catch (\Exception $e) {
            Log::error('Bedrock invokeModel Error: ' . $e->getMessage());
            return null;
        }
    }

    public function generateResponse(string $userMessage, string $chatId, ?string $context = null): ?string
    {
        try {
            $cachedResponse = $this->findSemanticCachedResponse($userMessage);
            if ($cachedResponse) {
                ChatbotLog::create([
                    'telegram_chat_id' => $chatId,
                    'pesan_masuk' => $userMessage,
                    'balasan_ai' => $cachedResponse,
                    'tokens_used' => 0,
                ]);
                return $cachedResponse;
            }

            $systemPrompt = $context ? $this->getRAGSystemPrompt($context) : $this->getSystemPrompt();
            $fullPrompt = "{$systemPrompt}\n\nUser: {$userMessage}";

            $aiResponse = $this->invokeModel($fullPrompt, 1024, 0.5);
            if ($aiResponse === null) {
                return null;
            }

            ChatbotLog::create([
                'telegram_chat_id' => $chatId,
                'pesan_masuk' => $userMessage,
                'balasan_ai' => $aiResponse,
                'tokens_used' => 0,
            ]);

            return $aiResponse;

        } catch (\Exception $e) {
            Log::error('Bedrock Provider Error: ' . $e->getMessage());
            return null;
        }
    }

    public function fixCopywriting(string $text, ?string $title = null): ?string
    {
        try {
            $trimmedText = trim(strip_tags($text));
            if (empty($trimmedText)) {
                $prompt = "Buatlah satu artikel berita atau pengumuman desa yang lengkap, natural, mengalir dengan baik, informatif, dan sangat bagus berdasarkan judul berikut: \"" . ($title ?? 'Informasi Desa') . "\". Gunakan bahasa Indonesia yang baik, benar, formal namun tetap ramah dibaca oleh warga desa/desa. Format artikel menggunakan tag HTML standar (seperti tag p, strong, em, ul, li, br, dll). Jangan berikan penjelasan atau pengantar tambahan apapun, balas HANYA dengan kode HTML artikel tersebut secara langsung.";
            } else {
                $prompt = "Perbaiki dan sempurnakan copywriting tulisan artikel berita desa berikut dari segi ejaan (EYD/PUEBI), tata bahasa, kesantunan, kejelasan, dan alur keterbacaan agar formal, menarik, dan rapi untuk dibaca warga desa. Pertahankan tag HTML (seperti p, strong, em, ul, li, br, dll) yang sudah ada di dalam teks asli. Jangan berikan penjelasan, komentar, atau pengantar tambahan apapun, balas HANYA dengan teks artikel yang sudah diperbaiki secara langsung.\n\nTeks Asli:\n" . $text;
            }

            return $this->invokeModel($prompt, 2048, 0.3);

        } catch (\Exception $e) {
            Log::error('Bedrock Provider fixCopywriting Error: ' . $e->getMessage());
            return null;
        }
    }

    public function generateSeoMetadata(string $title, string $content): ?array
    {
        try {
            $prompt = "Tolong buatkan meta deskripsi (sangat penting: HARUS kurang dari 150 karakter terhitung spasi, jangan sampai melebihi 150 karakter, padat, profesional, ramah SEO, merangkum isi berita, tanpa emoji) dan kata kunci SEO (5-7 kata kunci dipisahkan dengan koma) berdasarkan judul dan konten berita desa berikut. Balas HANYA dengan format JSON valid seperti ini tanpa markdown/formatting tambahan: {\"meta_description\": \"...\", \"kata_kunci\": \"...\"}\n\nJudul: {$title}\nKonten: " . strip_tags($content);

            $text = $this->invokeModel($prompt, 1024, 0.3);
            if ($text === null) {
                return null;
            }

            $text = trim($text);
            if (str_starts_with($text, '```json')) {
                $text = substr($text, 7);
            }
            if (str_ends_with($text, '```')) {
                $text = substr($text, 0, -3);
            }
            $text = trim($text);

            $decoded = json_decode($text, true);
            if (is_array($decoded) && isset($decoded['meta_description']) && isset($decoded['kata_kunci'])) {
                $metaDesc = trim($decoded['meta_description']);
                if (mb_strlen($metaDesc) > 160) {
                    $metaDesc = mb_substr($metaDesc, 0, 157) . '...';
                }
                $decoded['meta_description'] = $metaDesc;
                return $decoded;
            }

            Log::error('Bedrock generateSeoMetadata: response is not valid JSON');
            return null;

        } catch (\Exception $e) {
            Log::error('Bedrock Provider generateSeoMetadata Error: ' . $e->getMessage());
            return null;
        }
    }

    public function checkHealth(): bool
    {
        try {
            $result = $this->client->listFoundationModels();
            return $result->hasKey('modelSummaries');
        } catch (\Exception $e) {
            return false;
        }
    }
}
