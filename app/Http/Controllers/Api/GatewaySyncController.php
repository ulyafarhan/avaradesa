<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BotKnowledge;
use App\Models\KategoriSurat;
use App\Models\PengaturanDesa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Endpoint sinkronisasi data dari Laravel → wa-gateway.
 *
 * Gateway memanggil endpoint ini tiap 5 menit untuk mendapatkan:
 * - FAQ + Knowledge Base (BotKnowledge)
 * - Template notifikasi (pengaturan_desa)
 * - Kategori surat (syarat dokumen)
 *
 * @see docs/proposal-wa-gateway-v2.md — Fitur P6
 */
class GatewaySyncController extends Controller
{
    /**
     * GET /api/v1/gateway/sync
     *
     * Return semua data yang perlu gateway miliki untuk auto-reply.
     * Parameter: since (ISO timestamp, opsional — default 7 hari lalu).
     */
    public function sync(Request $request): JsonResponse
    {
        $since = $request->query('since')
            ? \Carbon\Carbon::parse($request->query('since'))
            : now()->subDays(7);

        // 1. FAQ + KB dari BotKnowledge
        $faqs = BotKnowledge::where('is_aktif', true)
            ->where('updated_at', '>=', $since)
            ->select('id', 'kunci', 'tipe', 'pertanyaan_atau_topik', 'kata_kunci', 'jawaban_atau_konten', 'is_aktif', 'updated_at')
            ->get();

        // 2. Kategori surat (syarat dokumen untuk document validation di gateway)
        $kategori = KategoriSurat::where('is_active', true)
            ->select('id', 'kode_surat', 'nama_surat', 'syarat_dokumen', 'updated_at')
            ->get();

        // 3. Template notifikasi dari pengaturan_desa
        $notifKeys = PengaturanDesa::query()
            ->where('kunci', 'like', 'notif_%')
            ->pluck('nilai', 'kunci')
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'faqs' => $faqs,
                'kategori_surat' => $kategori,
                'notif_templates' => $notifKeys,
                'synced_at' => now()->toIso8601String(),
            ],
        ]);
    }
}
