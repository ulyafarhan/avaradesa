<?php

namespace App\Filament\Resources\BotKnowledgeResource\Pages;

use App\Filament\Resources\BotKnowledgeResource;
use Filament\Resources\Pages\ManageRecords;

/**
 * Halaman manajemen basis pengetahuan chatbot Telegram.
 *
 * Menyediakan fitur CRUD penuh untuk data FAQ (jawaban instan) dan RAG KB (konteks AI)
 * yang digunakan oleh bot Telegram. Data ini menjadi sumber referensi otomatis bagi
 * chatbot dalam merespon pertanyaan warga.
 *
 * @see \App\Filament\Resources\BotKnowledgeResource
 * @see \App\Models\BotKnowledge
 */
class ManageBotKnowledge extends ManageRecords
{
    protected static string $resource = BotKnowledgeResource::class;
}
