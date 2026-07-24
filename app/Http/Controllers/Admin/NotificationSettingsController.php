<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationSettingsController extends Controller
{
    public function __invoke(Request $request)
    {
        $waProvider = config('services.whatsapp.provider', 'wa-gateway');

        return Inertia::render('Admin/NotificationSettings', [
            'tgTargetCount' => Penduduk::whereNotNull('telegram_chat_id')->count(),
            'tgBotInfo' => [
                'token_set' => !empty(config('services.telegram.bot_token')),
                'webhook_url' => config('services.telegram.webhook_url'),
            ],
            'waConfig' => [
                'provider' => $waProvider,
                'gateway_url' => config('services.whatsapp.gateway_url'),
                'session_id' => config('services.whatsapp.session_id', 'default'),
                'fonnte_token_set' => !empty(config('services.whatsapp.fonnte_token')),
            ],
        ]);
    }
}
