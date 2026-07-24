<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;
use Spatie\Activitylog\Models\Activity;

class MigrateAuditLogs extends Command
{
    protected $signature = 'log:migrate-from-audit {--dry-run : Hanya hitung, tanpa insert}';

    protected $description = 'Migrate data dari tabel audit_logs (legacy) ke activity_log (spatie)';

    public function handle(): int
    {
        $total = AuditLog::count();
        $this->info("Found $total audit_logs records to migrate.");
        if ($total === 0) {
            return Command::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->info('Dry run — no records inserted.');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $inserted = 0;
        AuditLog::query()->chunk(100, function ($logs) use (&$inserted, $bar) {
            foreach ($logs as $audit) {
                $eventMap = [
                    'create' => 'created',
                    'update' => 'updated',
                    'delete' => 'deleted',
                    'login' => 'auth.login',
                    'approve' => 'approved',
                    'reject' => 'rejected',
                ];
                $event = $eventMap[strtolower($audit->tindakan)] ?? $audit->tindakan;

                $activity = activity()
                    ->event($event)
                    ->withProperties([
                        'ip' => $audit->ip_address,
                        'user_agent' => $audit->user_agent,
                        'diff' => [
                            'data_lama' => $audit->data_lama,
                            'data_baru' => $audit->data_baru,
                        ],
                    ])
                    ->log($audit->tindakan . ' di ' . ($audit->nama_tabel ?? 'unknown'));

                if ($audit->user_type === 'admin') {
                    $activity->causer_type = 'App\Models\Administrator';
                    $activity->causer_id = $audit->user_id;
                } elseif ($audit->user_type === 'warga') {
                    $activity->causer_type = 'App\Models\Penduduk';
                    $activity->causer_id = $audit->user_id;
                }

                $extraProps = $activity->properties ?? collect([]);
                $extraProps = $extraProps instanceof \Illuminate\Support\Collection ? $extraProps->toArray() : (array) $extraProps;
                $extraProps['subject_type'] = $audit->nama_tabel;
                $extraProps['subject_id'] = $audit->record_id;
                $activity->properties = $extraProps;

                $activity->save();
                $inserted++;
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("Migrated $inserted records to activity_log.");

        return Command::SUCCESS;
    }
}
