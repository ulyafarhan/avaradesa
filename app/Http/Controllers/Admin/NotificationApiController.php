<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TelegramService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotificationApiController extends Controller
{
    // ── WhatsApp Gateway Proxy ──────────────────────────────────────────

    public function waStatus(WhatsAppService $wa): \Illuminate\Http\JsonResponse
    {
        $provider = $wa->getProvider();

        if ($provider === 'fonnte') {
            return $this->fonnteStatus();
        }

        $url = rtrim(config('services.whatsapp.gateway_url', 'http://localhost:2785'), '/');
        $apiKey = config('services.whatsapp.api_key', '');
        $sessionId = config('services.whatsapp.session_id', 'default');

        try {
            $response = Http::timeout(5)
                ->withHeaders(['X-API-Key' => $apiKey])
                ->get("{$url}/api/sessions/{$sessionId}/status");

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'provider' => 'wa-gateway',
                    'connected' => ($data['status'] ?? '') === 'connected',
                    'connection' => $data['status'] ?? 'unknown',
                    'reconnect_count' => $data['reconnect_count'] ?? 0,
                ]);
            }

            return response()->json([
                'connected' => false,
                'connection' => 'gateway_online',
                'provider' => 'wa-gateway',
                'session' => $sessionId,
            ]);
        } catch (\Exception) {
            return response()->json(['connected' => false, 'connection' => 'unreachable', 'provider' => 'wa-gateway']);
        }
    }

    public function waQr(WhatsAppService $wa): \Illuminate\Http\JsonResponse
    {
        if ($wa->getProvider() === 'fonnte') {
            return response()->json([
                'provider' => 'fonnte',
                'message' => 'Fonnte tidak memerlukan QR. Hubungkan melalui Fonnte App.',
            ]);
        }

        $url = rtrim(config('services.whatsapp.gateway_url', 'http://localhost:2785'), '/');
        $apiKey = config('services.whatsapp.api_key', '');
        $sessionId = config('services.whatsapp.session_id', 'default');

        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-API-Key' => $apiKey])
                ->get("{$url}/api/sessions/{$sessionId}/qr");

            return response()->json($response->json());
        } catch (\Exception) {
            return response()->json(['message' => 'Gateway tidak dapat diakses']);
        }
    }

    public function waTest(Request $request, WhatsAppService $wa): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'chat_id' => 'required|string',
            'message' => 'required|string|max:4096',
        ]);

        $ok = $wa->sendMessage($request->chat_id, $request->message);

        return $ok
            ? response()->json(['success' => true, 'provider' => $wa->getProvider()])
            : response()->json(['error' => 'Pengiriman gagal', 'provider' => $wa->getProvider()], 503);
    }

    protected function fonnteStatus(): \Illuminate\Http\JsonResponse
    {
        $token = config('services.whatsapp.fonnte_token', '');

        if (empty($token)) {
            return response()->json(['connected' => false, 'reason' => 'Token Fonnte belum dikonfigurasi', 'provider' => 'fonnte']);
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders(['Authorization' => $token])
                ->get('https://api.fonnte.com/device');

            $body = $response->json();
            $connected = $body['status'] ?? false;

            return response()->json([
                'connected' => $connected,
                'provider' => 'fonnte',
                'device' => $body['data'] ?? null,
            ]);
        } catch (\Exception) {
            return response()->json(['connected' => false, 'reason' => 'Fonnte tidak dapat diakses', 'provider' => 'fonnte']);
        }
    }

    // ── Telegram Proxy ──────────────────────────────────────────────────

    public function tgStatus(TelegramService $tg): \Illuminate\Http\JsonResponse
    {
        $bot = $tg->getMe();

        if (!$bot) {
            return response()->json(['connected' => false, 'error' => 'Token bot tidak valid']);
        }

        return response()->json([
            'connected' => true,
            'bot_name' => $bot['first_name'] ?? null,
            'bot_username' => $bot['username'] ?? null,
            'bot_id' => $bot['id'] ?? null,
        ]);
    }

    public function tgBroadcast(Request $request, TelegramService $tg): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'target' => 'required|string|in:all,custom',
            'chat_ids' => 'required_if:target,custom|array',
            'chat_ids.*' => 'string',
            'message' => 'required|string|max:4096',
        ]);

        if ($request->target === 'custom') {
            $chatIds = $request->chat_ids;
        } else {
            $chatIds = \App\Models\Penduduk::whereNotNull('telegram_chat_id')
                ->pluck('telegram_chat_id')
                ->toArray();
        }

        if (empty($chatIds)) {
            return response()->json(['error' => 'Tidak ada target penerima'], 422);
        }

        $result = $tg->broadcast($chatIds, $request->message);

        return response()->json($result);
    }
}
