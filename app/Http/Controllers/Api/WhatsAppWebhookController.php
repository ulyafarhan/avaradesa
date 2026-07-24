<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WaWebhookLog;
use App\Services\SystemLogger;
use App\Services\TelegramKnowledgeService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    public function handle(Request $request, TelegramKnowledgeService $knowledge, WhatsAppService $wa): \Illuminate\Http\JsonResponse
    {
        $apiKey = $request->header('X-API-Key');
        $expected = config('services.whatsapp.api_key');

if (!$expected || !hash_equals((string) $expected, (string) ($apiKey ?? ''))) {
            Log::warning('Invalid WhatsApp webhook API key', [
                'ip' => $request->ip(),
            ]);
            SystemLogger::log('webhook.received', 'Webhook WhatsApp ditolak (invalid key)', null, [
                'ip' => $request->ip(), 'result' => 'rejected',
            ]);
            abort(401, 'Invalid API key');
        }

        Log::info('WhatsApp webhook received', [
            'from' => $request->input('data.message.from', 'unknown'),
            'session' => $request->input('data.session_id', 'unknown'),
        ]);

        SystemLogger::log('webhook.received', 'Webhook WhatsApp diterima', null, [
            'from' => $request->input('data.message.from', 'unknown'),
            'session' => $request->input('data.session_id', 'unknown'),
            'event' => $request->input('event', 'message'),
        ]);

        $message = $request->input('data.message');
        if ($message) {
            $from = $message['from'] ?? '';
            $text = $message['text']['body'] ?? '';

WaWebhookLog::create([
                'event' => 'message.incoming',
                'session_id' => $request->input('data.session_id', 'unknown'),
                'sender' => $from,
                'text' => $text,
                'wa_message_id' => $message['id'] ?? null,
                'raw_payload' => ['truncated' => true],
                'event_at' => now(),
            ]);

            if ($from && $text) {
                $answer = $knowledge->findStaticAnswer($text);
                if ($answer) {
                    $wa->sendMessage($from, $answer);
                }
            }
        } else {
            WaWebhookLog::create([
                'event' => $request->input('event', 'unknown'),
                'session_id' => $request->input('data.session_id', 'unknown'),
                'raw_payload' => ['truncated' => true],
                'event_at' => now(),
            ]);
        }

        return response()->json(['ok' => true]);
    }
}
