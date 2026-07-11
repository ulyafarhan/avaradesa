<?php

namespace App\Filament;

use App\Filament\Auth\AdminLogin;
use App\Filament\Pages\Dashboard as AdminDashboard;
use App\Filament\Widgets\AdminStatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * Penyedia konfigurasi panel admin Filament (SIG-Udeung).
 * Menentukan tema warna, grup navigasi, resource, halaman, widget, serta middleware panel.
 */
class AdminPanelProvider extends PanelProvider
{
    /**
     * Mengonfigurasi properti panel Filament admin.
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login(AdminLogin::class)
            ->authGuard('admin')
            ->brandLogo(fn () => view('filament.logo'))
            ->brandLogoHeight('2.75rem')
            ->favicon('/favicon.ico')
            ->colors([
                'primary' => Color::Teal,
                'gray' => Color::Slate,
                'danger' => Color::Rose,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'info' => Color::Sky,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->font('Inter')
            ->darkMode(true)
            ->sidebarFullyCollapsibleOnDesktop()
            ->sidebarWidth('17rem')
            ->globalSearch()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->breadcrumbs()
            ->databaseNotifications()
            ->databaseNotificationsPolling('15s')
            ->spa()
            ->unsavedChangesAlerts()
            ->renderHook(
                'panels::head.end',
                fn () => view('filament.custom-styles'),
            )
            ->renderHook(
                'panels::auth.login.form.after',
                fn () => view('filament.login-credentials'),
            )
            ->navigationGroups([
                NavigationGroup::make('Layanan')
                    ->collapsible(),
                NavigationGroup::make('Data Kependudukan')
                    ->collapsible(),
                NavigationGroup::make('Konten')
                    ->collapsible(),
                NavigationGroup::make('Pengaturan')
                    ->collapsible()
                    ->collapsed(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                AdminDashboard::class,
            ])
            ->widgets([
                AdminStatsOverview::class,
                \App\Filament\Widgets\ServerPerformanceWidget::class,
                \App\Filament\Widgets\TrafficChartWidget::class,
                \App\Filament\Widgets\RecentSubmissionsWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
