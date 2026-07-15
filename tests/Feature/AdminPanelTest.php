<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Administrator;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_users_can_access_panel(): void
    {
        $roles = ['kepala_desa', 'sekdes', 'operator'];

        foreach ($roles as $role) {
            $admin = Administrator::factory()->create([
                'role' => $role,
            ]);

            $this->assertTrue($admin->canAccessPanel(Filament::getCurrentPanel() ?? Filament::getPanel('admin')));
        }
    }

    public function test_unauthorized_users_cannot_access_panel(): void
    {
        $admin = Administrator::factory()->make([
            'role' => 'unauthorized_role',
        ]);

        $this->assertFalse($admin->canAccessPanel(Filament::getCurrentPanel() ?? Filament::getPanel('admin')));
    }

    public function test_unauthenticated_cannot_access_panel_http(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect();
    }
}
