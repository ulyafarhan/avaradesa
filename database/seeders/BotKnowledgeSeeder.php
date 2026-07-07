<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BotKnowledge;
use Illuminate\Support\Str;
use Database\Seeders\knowledge\BotKnowledgeFaqAdministrasi;
use Database\Seeders\knowledge\BotKnowledgeFaqLayananPendidikan;
use Database\Seeders\knowledge\BotKnowledgeFaqKesehatanEkonomi;
use Database\Seeders\knowledge\BotKnowledgeFaqAgamaSosialUmum;
use Database\Seeders\knowledge\BotKnowledgeProsedurSistem;
use Database\Seeders\knowledge\BotKnowledgeTambahan;

class BotKnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        BotKnowledge::query()->delete();

        $allKnowledge = array_merge(
            (new BotKnowledgeFaqAdministrasi())->getFaqAdministrasi(),
            (new BotKnowledgeFaqLayananPendidikan())->getFaqLayanan(),
            (new BotKnowledgeFaqLayananPendidikan())->getFaqPendidikan(),
            (new BotKnowledgeFaqKesehatanEkonomi())->getFaqKesehatan(),
            (new BotKnowledgeFaqKesehatanEkonomi())->getFaqEkonomi(),
            (new BotKnowledgeFaqAgamaSosialUmum())->getFaqKeagamaan(),
            (new BotKnowledgeFaqAgamaSosialUmum())->getFaqSosial(),
            (new BotKnowledgeFaqAgamaSosialUmum())->getFaqUmum(),
            (new BotKnowledgeProsedurSistem())->getProsedur(),
            (new BotKnowledgeProsedurSistem())->getInformasiSistem(),
            (new BotKnowledgeTambahan())->getTambahan(),
        );

        $insertData = array_map(function ($item) {
            return [
                'id' => Str::ulid(),
                'kunci' => $item['kunci'],
                'tipe' => $item['tipe'],
                'pertanyaan_atau_topik' => $item['pertanyaan_atau_topik'],
                'kata_kunci' => json_encode($item['kata_kunci'], JSON_UNESCAPED_UNICODE),
                'jawaban_atau_konten' => $item['jawaban_atau_konten'],
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $allKnowledge);

        foreach (array_chunk($insertData, 100) as $chunk) {
            BotKnowledge::insert($chunk);
        }
    }
}
