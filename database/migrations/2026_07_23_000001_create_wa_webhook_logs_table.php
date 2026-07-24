<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wa_webhook_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('event', 50);
            $table->string('session_id', 50);
            $table->string('sender', 100)->nullable();
            $table->text('text')->nullable();
            $table->string('wa_message_id', 100)->nullable();
            $table->json('raw_payload');
            $table->timestamp('event_at')->nullable();
            $table->timestamps();

            $table->index('event');
            $table->index('session_id');
            $table->index('sender');
            $table->index('event_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_webhook_logs');
    }
};
