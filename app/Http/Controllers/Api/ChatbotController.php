<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatbotLog;
use App\Models\BotKnowledge;

use App\Services\FallbackAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function __construct(
        protected FallbackAiService $aiService,
    ) {}

    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|min:1|max:2000',
            'session_id' => 'nullable|string|max:100',
        ]);

        $message = $request->input('message');
        $sessionId = $request->input('session_id', Str::random(32));
        $nik = $request->user()?->nik;

        $knowledge = BotKnowledge::inRandomOrder()->limit(5)->get();
        $context = $knowledge->pluck('answer')->implode("\n");

        try {
            $response = $this->aiService->generateResponse($message, $sessionId, $context);
            if (!$response) throw new \Exception("AI Provider failed");
        } catch (\Throwable $e) {
            $response = 'Maaf, saya sedang tidak dapat menjawab pertanyaan. Silakan hubungi admin desa.';
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
