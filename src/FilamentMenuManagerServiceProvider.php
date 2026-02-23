<?php

namespace NoteBrainsLab\FilamentMenuManager;

use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use NoteBrainsLab\FilamentMenuManager\Commands\InstallCommand;
use NoteBrainsLab\FilamentMenuManager\Livewire\MenuBuilder;
use NoteBrainsLab\FilamentMenuManager\Livewire\MenuPanel;

class FilamentMenuManagerServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-menu-manager';

    public static string $viewNamespace = 'filament-menu-manager';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasCommands([InstallCommand::class])
            ->hasMigrations([
                '2024_01_01_000001_create_menu_locations_table',
                '2024_01_01_000002_create_menus_table',
                '2024_01_01_000003_create_menu_items_table',
            ])
            ->hasViews(static::$viewNamespace)
            ->hasTranslations();
    }

    public function packageRegistered(): void
    {
        // Bind as singleton so it can be shared across requests
        $this->app->singleton(MenuManager::class);
    }

    public function packageBooted(): void
    {
        // Register Livewire components
        Livewire::component('filament-menu-manager.menu-builder', MenuBuilder::class);
        Livewire::component('filament-menu-manager.menu-panel', MenuPanel::class);

        // Register Filament assets
        \Filament\Support\Facades\FilamentAsset::register([
            \Filament\Support\Assets\Css::make(
                'filament-menu-manager-styles',
                __DIR__ . '/../resources/dist/filament-menu-manager.css'
            ),
            \Filament\Support\Assets\Js::make(
                'filament-menu-manager-scripts',
                __DIR__ . '/../resources/dist/filament-menu-manager.js'
            ),
        ], 'notebrainslab/filament-menu-manager');
    }
}
