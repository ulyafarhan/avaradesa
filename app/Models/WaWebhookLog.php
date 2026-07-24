<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class WaWebhookLog extends Model
{
    use HasUlids;

    protected $table = 'wa_webhook_logs';

    protected $fillable = [
        'event',
        'session_id',
        'sender',
        'text',
        'wa_message_id',
        'raw_payload',
        'event_at',
    ];

    protected function casts(): array
    {
        return [
            'raw_payload' => 'array',
            'event_at' => 'datetime',
        ];
    }
}
