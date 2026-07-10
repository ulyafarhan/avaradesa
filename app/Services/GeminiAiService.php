<?php

namespace App\Services;

use App\Services\Contracts\AiProviderInterface;

/**
 * Service yang membungkus AiProviderInterface untuk berbagai kebutuhan AI.
 *
 * Menyediakan akses terpusat ke fungsionalitas AI (respons chatbot,
 * perbaikan copywriting, dan meta data SEO) tanpa tergantung provider spesifik.
 */
class GeminiAiService
{
    /**
     * Instance provider AI aktif (Gemini/OpenAI).
     */
    protected AiProviderInterface $provider;

    /**
     * Menginisialisasi dengan provider AI dari service container.
     *
     * Mengambil instance AiProviderInterface yang terikat (bisa Gemini, OpenAI, atau FallbackAiService).
     */
    public function __construct()
    {
        $this->provider = app(AiProviderInterface::class);
    }

    /**
     * Menghasilkan respons AI untuk pesan pengguna.
     *
     * @param  string  $userMessage  Pesan yang dikirim oleh pengguna
     * @param  string  $chatId  ID chat Telegram untuk logging
     * @param  string|null  $context  Konteks RAG dari knowledge base (opsional)
     * @return string|null  Respons AI yang dihasilkan, atau null jika gagal
     */
    public function generateResponse(string $userMessage, string $chatId, ?string $context = null): ?string
    {
        return $this->provider->generateResponse($userMessage, $chatId, $context);
    }

    /**
     * Memperbaiki dan menyempurnakan copywriting artikel berita desa.
     *
     * @param  string  $text  Teks artikel yang akan diperbaiki
     * @param  string|null  $title  Judul artikel untuk konteks pembuatan konten baru (opsional)
     * @return string|null  Teks artikel yang sudah diperbaiki, atau null jika gagal
     */
    public function fixCopywriting(string $text, ?string $title = null): ?string
    {
        return $this->provider->fixCopywriting($text, $title);
    }

    /**
     * Menghasilkan meta deskripsi dan kata kunci SEO untuk artikel berita.
     *
     * @param  string  $title  Judul artikel berita
     * @param  string  $content  Konten artikel berita (HTML)
     * @return array|null  Assoc array dengan 'meta_description' dan 'kata_kunci', atau null jika gagal
     */
    public function generateSeoMetadata(string $title, string $content): ?array
    {
        return $this->provider->generateSeoMetadata($title, $content);
    }

    /**
     * Memeriksa ketersediaan layanan provider AI.
     *
     * @return bool  true jika provider AI merespons dengan sehat
     */
    public function checkHealth(): bool
    {
        return $this->provider->checkHealth();
    }
}
