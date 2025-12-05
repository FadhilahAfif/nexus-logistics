<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\RecentShipments;
use App\Filament\Widgets\ShipmentStatsOverview;
use App\Filament\Widgets\ShipmentStatusChart;
use App\Filament\Widgets\ShipmentStatusDistributionChart;
use Filament\Navigation\MenuItem;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            // --- BAGIAN BRANDING (KITA UBAH DISINI) ---
            ->brandName('Nexus Logistics') // Ganti tulisan Laravel jadi Nexus
            ->colors([
                'primary' => Color::Indigo, // Ganti warna oranye jadi Indigo (Biru Ungu)
            ])
            ->font('Inter') // Pakai font yang modern
            ->favicon(asset('favicon.ico')) // Icon di tab browser (opsional)
            // ------------------------------------------
            
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                ShipmentStatsOverview::class,
                ShipmentStatusChart::class,
                ShipmentStatusDistributionChart::class,
                RecentShipments::class,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Kembali ke Website')
                    ->url('/')
                    ->icon('heroicon-o-home'),
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
            ])
            // Fitur Tambahan biar lebih canggih
            ->sidebarCollapsibleOnDesktop() // Sidebar bisa dilipat
            ->spa(); // Single Page Application (Loading kilat tanpa refresh)
    }
}