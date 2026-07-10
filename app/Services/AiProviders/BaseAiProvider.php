<?php

namespace App\Services\AiProviders;

use App\Models\ChatbotLog;
use Illuminate\Support\Facades\Cache;

abstract class BaseAiProvider
{
    /**
     * Menyusun system prompt dengan konteks RAG dari dokumen referensi.
     */
    protected function getRAGSystemPrompt(string $context): string
    {
        return "Anda adalah asisten virtual resmi Desa.\nJawablah pertanyaan warga secara sopan dan formal HANYA berdasarkan dokumen referensi berikut:\n\n---\nDOKUMEN REFERENSI:\n{$context}\n---\n\nJika informasi tidak ada dalam dokumen referensi di atas, jawab dengan sopan bahwa Anda tidak memiliki informasi detail mengenai hal tersebut dan sarankan untuk menghubungi kantor desa.";
    }

    /**
     * Menyusun system prompt default untuk percakapan umum dengan warga.
     */
    protected function getSystemPrompt(): string
    {
        return "Anda adalah asisten virtual resmi sistem AvaraDesa.\n\nTUGAS ANDA:\n1. Menjawab pertanyaan warga tentang prosedur administrasi desa\n2. Memberikan informasi tentang persyaratan surat-menyurat\n3. Menjelaskan cara menggunakan sistem AvaraDesa\n4. Memberikan informasi umum tentang desa\n\nJENIS SURAT YANG TERSEDIA:\n- Surat Keterangan Domisili\n- Surat Keterangan Tidak Mampu (SKTM)\n- Surat Keterangan Usaha\n- Surat Pengantar KTP\n- Surat Keterangan Kelahiran\n\nPROSEDUR UMUM:\n1. Login ke PWA menggunakan NIK\n2. Pilih jenis surat yang dibutuhkan\n3. Isi formulir dan upload dokumen persyaratan\n4. Tunggu verifikasi dari perangkat desa\n5. Surat akan dikirim via Telegram jika disetujui\n\nMUTASI KEPENDUDUKAN:\n- Kelahiran: Upload akta kelahiran\n- Kematian: Upload surat kematian dari RS/Puskesmas\n- Kedatangan: Upload surat pindah dari desa asal\n- Kepindahan: Ajukan surat pindah\n\nATURAN KOMUNIKASI:\n- Gunakan bahasa Indonesia yang sopan dan formal\n- Jika tidak tahu jawaban, arahkan untuk menghubungi kantor desa\n- Jangan memberikan informasi pribadi warga\n- Fokus pada informasi prosedural dan administratif\n\nJawab pertanyaan berikut dengan jelas dan membantu:";
    }

    /**
     * Menemukan respons cache berdasarkan kecocokan semantik dengan pertanyaan sebelumnya.
     */
    protected function findSemanticCachedResponse(string $userMessage): ?string
    {
        $normalized = trim(strtolower($userMessage));
        
        $cacheKey = 'ai_exact_' . md5($normalized);
        $cachedValue = Cache::get($cacheKey);
        if ($cachedValue) {
            return $cachedValue;
        }

        $exactLog = ChatbotLog::whereRaw('LOWER(TRIM(pesan_masuk)) = ?', [$normalized])
            ->where('created_at', '>=', now()->subHours(24))
            ->whereNotNull('balasan_ai')
            ->orderBy('created_at', 'desc')
            ->first();
        if ($exactLog) {
            Cache::put($cacheKey, $exactLog->balasan_ai, 86400);
            return $exactLog->balasan_ai;
        }

        $recentLogs = Cache::remember('ai_recent_logs_semantic', 300, function () {
            return ChatbotLog::where('created_at', '>=', now()->subHours(48))
                ->whereNotNull('balasan_ai')
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get(['pesan_masuk', 'balasan_ai'])
                ->unique('pesan_masuk')
                ->values();
        });

        if ($recentLogs->isEmpty()) {
            return null;
        }

        $bestScore = 0.0;
        $bestResponse = null;
        
        // Limit calculation string size for CPU performance
        if (strlen($normalized) > 1000) {
            return null;
        }
        
        $inputTokens = $this->tokenize($normalized);
        if (count($inputTokens) === 0) {
            return null;
        }

        foreach ($recentLogs as $log) {
            $logTokens = $this->tokenize(trim(strtolower($log->pesan_masuk)));
            if (count($logTokens) === 0) {
                continue;
            }

            $intersection = count(array_intersect($inputTokens, $logTokens));
            $union = count(array_unique(array_merge($inputTokens, $logTokens)));
            
            $score = $union > 0 ? ($intersection / $union) : 0;
            
            if (strlen($normalized) < 20) {
                $levDist = levenshtein($normalized, trim(strtolower($log->pesan_masuk)));
                $maxLen = max(strlen($normalized), strlen($log->pesan_masuk));
                $levScore = $maxLen > 0 ? (1 - ($levDist / $maxLen)) : 0;
                $score = max($score, $levScore);
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestResponse = $log->balasan_ai;
            }
        }

        if ($bestScore >= 0.80 && $bestResponse) {
            Cache::put($cacheKey, $bestResponse, 86400);
            return $bestResponse;
        }

        return null;
    }

    /**
     * Melakukan tokenisasi dan filtering stopwords untuk pencocokan semantik.
     */
    protected function tokenize(string $text): array
    {
        $clean = preg_replace('/[^\w\s]/u', '', $text);
        $words = preg_split('/\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY);
        
        $stopwords = ['yang', 'di', 'dan', 'itu', 'dengan', 'untuk', 'pada', 'ke', 'dari', 'ini', 'adalah', 'akan', 'atau', 'saya', 'anda', 'kami', 'kita'];
        return array_diff($words, $stopwords);
    }
}
