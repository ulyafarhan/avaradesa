<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penduduk', function (Blueprint $table) {
            $table->string('pin_hash')->nullable()->after('telegram_chat_id');
            $table->integer('pin_attempts')->default(0)->after('pin_hash');
            $table->timestamp('locked_until')->nullable()->after('pin_attempts');
        });
    }
    public function down(): void
    {
        Schema::table('penduduk', function (Blueprint $table) {
            $table->dropColumn(['pin_hash', 'pin_attempts', 'locked_until']);
        });
    }
};
