<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengajuanSurat;
use App\Models\MutasiPenduduk;
use App\Models\Penduduk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SyncController extends Controller
{
    /**
     * Pull Sync Data
     *
     * Mengambil perubahan data sejak timestamp terakhir sinkronisasi.
     *
     * @authenticated
     * @queryParam since string required Timestamp sinkronisasi terakhir (ISO8601). Example: 2026-07-13T10:00:00Z
     * @response 200 {"data": {"pengajuan_surat": {"updated": [], "deleted": []}, "mutasi": {"updated": [], "deleted": []}, "penduduk": null}, "meta": {"sync_token": "2026-07-14T..."}}
     */
    public function pull(Request $request): JsonResponse
    {
        $request->validate([
            'since' => 'required|date_format:Y-m-d\TH:i:s\Z',
        ]);

        $since = $request->input('since');
        $nik = $request->user()->nik;

        $pengajuanSurat = PengajuanSurat::where('nik_pemohon', $nik)
            ->where('updated_at', '>', $since)
            ->get();

        $mutasi = MutasiPenduduk::where('nik', $nik)
            ->where('created_at', '>', $since)
            ->get();

        $penduduk = Penduduk::where('nik', $nik)->first();

        return response()->json([
            'data' => [
                'pengajuan_surat' => [
                    'updated' => $pengajuanSurat,
                    'deleted' => [],
                ],
                'mutasi' => [
                    'updated' => $mutasi,
                    'deleted' => [],
                ],
                'penduduk' => $penduduk?->fresh(),
            ],
            'meta' => [
                'sync_token' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Push Sync Data
     *
     * Mengirim operasi offline ke server.
     *
     * @authenticated
     * @bodyParam operations array required Daftar operasi yang dilakukan offline. Example: [{"client_id": "550e8400-e29b-41d4-a716-446655440000", "type": "pengajuan_surat", "action": "create", "data": {"kategori_surat_id": 1}, "created_at": "2026-07-14T10:00:00Z"}]
     * @response 200 {"status": "processed", "results": [{"client_id": "550e...", "status": "success", "server_id": 42}], "server_sync_token": "2026-07-14T..."}
     */
    public function push(Request $request): JsonResponse
    {
        $request->validate([
            'operations' => 'required|array',
            'operations.*.client_id' => 'required|string|uuid',
            'operations.*.type' => 'required|in:pengajuan_surat,mutasi',
            'operations.*.action' => 'required|in:create',
            'operations.*.data' => 'required|array',
            'operations.*.created_at' => 'required|date_format:Y-m-d\TH:i:s\Z',
        ]);

        $nik = $request->user()->nik;
        $results = [];

        foreach ($request->input('operations') as $op) {
            try {
                DB::beginTransaction();

                if ($op['type'] === 'pengajuan_surat' && $op['action'] === 'create') {
                    $pengajuan = PengajuanSurat::create([
                        'nik_pemohon' => $nik,
                        'kategori_surat_id' => $op['data']['kategori_surat_id'] ?? 1,
                        'data_isian' => $op['data']['data_isian'] ?? [],
                        'status' => 'Pending',
                    ]);

                    $results[] = [
                        'client_id' => $op['client_id'],
                        'status' => 'success',
                        'server_id' => $pengajuan->id,
                    ];
                } elseif ($op['type'] === 'mutasi' && $op['action'] === 'create') {
                    $mutasi = MutasiPenduduk::create([
                        'nik' => $nik,
                        'jenis_mutasi' => $op['data']['jenis_mutasi'] ?? '',
                        'tanggal_mutasi' => $op['data']['tanggal_mutasi'] ?? now(),
                        'keterangan' => $op['data']['alasan'] ?? '',
                        'status_verifikasi' => 'Pending',
                    ]);

                    $results[] = [
                        'client_id' => $op['client_id'],
                        'status' => 'success',
                        'server_id' => $mutasi->id,
                    ];
                }

                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                $results[] = [
                    'client_id' => $op['client_id'],
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'status' => 'processed',
            'results' => $results,
            'server_sync_token' => now()->toIso8601String(),
        ]);
    }
}
