<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller untuk menangani kiriman pesan/webhook masuk dari WhatsApp Gateway.
 *
 * Berguna jika di masa mendatang dikembangkan fitur auto-reply chatbot
 * interaktif via WhatsApp menggunakan LLM/AI.
 */
class WhatsAppWebhookController extends Controller
{
    /**
     * Menangani request payload webhook dari WhatsApp gateway.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request): \Illuminate\Http\JsonResponse
    {
        // Log pesan masuk untuk keperluan debugging & audit trail
        Log::info('WhatsApp Webhook Received', $request->all());

        $message = $request->input('data.message');
        if ($message) {
            $from = $message['from'] ?? '';
            $text = $message['text']['body'] ?? '';

            // Sesi ini dapat dihubungkan ke FallbackAiService/Gemini jika ingin auto-reply
            Log::info("WhatsApp pesan dari: {$from}, teks: {$text}");
        }

        return response()->json(['ok' => true]);
    }
}
