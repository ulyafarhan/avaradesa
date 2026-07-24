<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class SystemLogger
{
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
