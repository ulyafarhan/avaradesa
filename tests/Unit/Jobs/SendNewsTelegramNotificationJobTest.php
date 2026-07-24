<?php

namespace Tests\Unit\Jobs;

use App\Jobs\SendNewsTelegramNotificationJob;
use App\Models\InformasiPublik;
use App\Services\TelegramService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Mockery\MockInterface;

class SendNewsTelegramNotificationJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_sends_published_news_with_html_format()
    {
        config(['services.telegram.group_chat_id' => 'group_123']);

        $news = InformasiPublik::factory()->create([
            'judul' => 'Berita Desa Baru',
            'kategori' => 'Kesehatan Masyarakat',
            'konten' => '<p>Ini adalah isi berita yang cukup panjang untuk dites.</p>',
            'is_published' => true,
            'cover_image' => 'http://localhost/storage/cover.jpg',
            'slug' => 'berita-desa-baru'
        ]);

        $telegram = $this->mock(TelegramService::class, function (MockInterface $mock) {
            $mock->shouldReceive('sendPhoto')
                 ->once()
                 ->with('group_123', \Mockery::type('string'), \Mockery::on(function ($msg) {
                     return str_contains($msg, 'BERITA & PENGUMUMAN DESA') &&
                            str_contains($msg, 'Berita Desa Baru') &&
                            str_contains($msg, '#KesehatanMasyarakat') &&
                            str_contains($msg, 'Baca Selengkapnya di SIG-Udeung');
                 }))
                 ->andReturn(true);
        });

        $job = new SendNewsTelegramNotificationJob($news->id);
        $job->handle($telegram);
    }
}
