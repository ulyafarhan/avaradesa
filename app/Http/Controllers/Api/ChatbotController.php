<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatbotLog;
use App\Models\BotKnowledge;
use App\Services\GeminiAiService;
use App\Services\FallbackAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __construct(
        protected GeminiAiService $gemini,
        protected FallbackAiService $fallback,
    ) {}

    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|min:1|max:2000',
            'session_id' => 'nullable|string|max:100',
        ]);

        $message = $request->input('message');
        $sessionId = $request->input('session_id', uniqid('chat_', true));
        $nik = $request->user()?->nik;

        $knowledge = BotKnowledge::inRandomOrder()->limit(5)->get();
        $context = $knowledge->pluck('answer')->implode("\n");

        try {
            $response = $this->gemini->generateResponse($message, $sessionId, $context);
        } catch (\Throwable $e) {
            try {
                $response = $this->fallback->generateResponse($message, $sessionId, $context);
            } catch (\Throwable $e2) {
                $response = 'Maaf, saya sedang tidak dapat menjawab pertanyaan. Silakan hubungi admin desa.';
            }
        }

        ChatbotLog::create([
            'nik' => $nik ?? 'guest',
            'pertanyaan' => $message,
            'jawaban' => $response,
            'session_id' => $sessionId,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'response' => $response,
                'session_id' => $sessionId,
            ],
        ]);
    }
}
