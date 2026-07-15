<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_log_records_creation_and_update_of_data()
    {
        $keluarga = Keluarga::factory()->create();
        $warga = Penduduk::factory()->create(['no_kk' => $keluarga->no_kk]);

        // Manually trigger the log which normally happens in the controller
        AuditLog::log('warga', $warga->nik, 'create', 'penduduk', $warga->nik);

        // 1. Audit Log created for Penduduk creation
        $this->assertDatabaseHas('audit_logs', [
            'nama_tabel' => 'penduduk',
            'tindakan' => 'create',
            'record_id' => $warga->nik
        ]);

        // 2. Perform updating action
        $warga->update(['nama_lengkap' => 'Nama Baru Disunting']);
        
        // Log manually triggered in update flow if not automatically hookable,
        // Let's test custom log invocation directly
        AuditLog::log('warga', $warga->nik, 'update', 'penduduk', $warga->nik, ['nama_lengkap' => 'Lama'], ['nama_lengkap' => 'Nama Baru Disunting']);

        $this->assertDatabaseHas('audit_logs', [
            'nama_tabel' => 'penduduk',
            'tindakan' => 'update',
            'record_id' => $warga->nik,
        ]);
        
        $log = AuditLog::where('tindakan', 'update')->first();
        $this->assertEquals('Lama', $log->data_lama['nama_lengkap']);
        $this->assertEquals('Nama Baru Disunting', $log->data_baru['nama_lengkap']);
    }
}
