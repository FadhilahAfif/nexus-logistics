<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        // 1. Mencegah error panjang key string di MySQL lama
        Schema::defaultStringLength(191);

        // 2. Mematikan proteksi mass-assignment (Biar kita ga capek nulis $fillable di tiap model)
        // Aman karena kita pakai validasi di Filament/Controller.
        Model::unguard();

        // 3. Mode Strict (Hanya di Local) - Mencegah query lambat (N+1 Problem)
        // Ini fitur advanced biar lo tau kalo codingan lo bikin server berat.
        Model::preventLazyLoading(! app()->isProduction());

        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            fn (): string => Blade::render("@include('filament.hooks.language-switch')")
        );
    }
}