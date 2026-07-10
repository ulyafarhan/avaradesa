<?php

namespace App\Services\Contracts;

/**
 * Kontrak untuk provider AI yang digunakan sistem.
 *
 * Mendefinisikan method yang harus diimplementasikan oleh setiap
 * provider AI (Gemini, OpenAI, dll) untuk respons chatbot,
 * perbaikan copywriting, meta data SEO, dan pengecekan kesehatan.
 */
interface AiProviderInterface
{
    /**
     * Menghasilkan respons AI untuk pesan pengguna.
     *
     * @param  string  $userMessage  Pesan yang dikirim oleh pengguna
     * @param  string  $chatId  ID chat Telegram untuk logging dan identifikasi sesi
     * @param  string|null  $context  Konteks RAG dari knowledge base (opsional)
     * @return string|null  Respons AI yang dihasilkan, atau null jika gagal
     */
    public function generateResponse(string $userMessage, string $chatId, ?string $context = null): ?string;

    /**
     * Memperbaiki dan menyempurnakan copywriting artikel berita desa.
     *
     * @param  string  $text  Teks artikel yang akan diperbaiki/disingkat
     * @param  string|null  $title  Judul artikel untuk konteks pembuatan konten baru (opsional)
     * @return string|null  Teks artikel yang sudah diperbaiki, atau null jika gagal
     */
    public function fixCopywriting(string $text, ?string $title = null): ?string;

    /**
     * Menghasilkan meta deskripsi dan kata kunci SEO untuk artikel berita.
     *
     * @param  string  $title  Judul artikel berita
     * @param  string  $content  Konten artikel berita (bisa dalam format HTML)
     * @return array|null  Assoc array dengan 'meta_description' dan 'kata_kunci', atau null jika gagal
     */
    public function generateSeoMetadata(string $title, string $content): ?array;

    /**
     * Memeriksa ketersediaan layanan provider AI.
     *
     * @return bool  true jika provider AI merespons dengan sehat
     */
    public function checkHealth(): bool;
}
