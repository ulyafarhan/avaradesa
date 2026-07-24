<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

/**
 * Service pencatatan log terpusat yang membungkus spatie/laravel-activitylog.
 * Mencatat aktivitas sistem dengan informasi penyebab (causer), subjek, dan properti tambahan.
 */
class SystemLogger
{
    /**
     * Mencatat aktivitas ke dalam log sistem.
     *
     * @param string $event Nama event (contoh: 'aspirasi.kirim', 'surat.dibuat').
     * @param string $description Deskripsi aktivitas yang terjadi.
     * @param mixed $subject Model Eloquent yang terkait dengan aktivitas (opsional).
     * @param array $properties Data tambahan yang akan dicatat.
     * @param string|null $logName Nama log channel (default: 'system').
     */
    public static function log(
        string $event,
        string $description,
        mixed $subject = null,
        array $properties = [],
        ?string $logName = 'system'
    ): void {
        $causer = null;

        if (Auth::guard('admin')->check()) {
            $causer = Auth::guard('admin')->user();
        } elseif (Auth::guard('web')->check()) {
            $causer = Auth::guard('web')->user();
        }

        $log = activity($logName)
            ->event($event)
            ->causedBy($causer);

        if ($subject !== null && is_object($subject)) {
            $log->performedOn($subject);
        }

        $log
            ->withProperties(array_merge($properties, [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]))
            ->log($description);
    }
}
