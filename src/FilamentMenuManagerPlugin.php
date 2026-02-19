<?php

namespace NoteBrainsLab\FilamentMenuManager;

use Filament\Contracts\Plugin;
use Filament\Panel;
use NoteBrainsLab\FilamentMenuManager\Pages\MenuManagerPage;

class FilamentMenuManagerPlugin implements Plugin
{
    protected array $locations   = [];
    protected array $modelSources = [];
    protected \UnitEnum|string|null $navigationGroup = null;
    protected string|\Illuminate\Contracts\Support\Htmlable|null $navigationIcon = null;
    protected ?int $navigationSort = null;
    protected string|\Illuminate\Contracts\Support\Htmlable|null $navigationLabel = null;

    // -------------------------------------------------------------------------
    // Static constructor (fluent API entry point)
    // -------------------------------------------------------------------------

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());
        return $plugin;
    }

    // -------------------------------------------------------------------------
    // Filament Plugin contract
    // -------------------------------------------------------------------------

    public function getId(): string
    {
        return 'filament-menu-manager';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([MenuManagerPage::class]);
    }

    public function boot(Panel $panel): void
    {
        /** @var MenuManager $manager */
        $manager = app(MenuManager::class);

        // Merge plugin-level locations with config locations
        $configLocations = config('filament-menu-manager.locations', []);
        $manager->registerLocations(array_merge($configLocations, $this->locations));

        // Register model sources
        $configSources = config('filament-menu-manager.model_sources', []);
        $manager->registerModelSources(array_merge($configSources, $this->modelSources));

        // Sync locations to database
        $manager->syncLocations();
    }

    // -------------------------------------------------------------------------
    // Fluent configuration methods
    // -------------------------------------------------------------------------

    /**
     * Register menu locations.
     *
     * @param  array  $locations  Associative ['handle' => 'Label'] or plain ['handle', ...]
     */
    public function locations(array $locations): static
    {
        $this->locations = $locations;
        return $this;
    }

    /**
     * Register Eloquent model classes as panel item sources.
     *
     * @param  array  $models  e.g. [Post::class, Page::class]
     */
    public function modelSources(array $models): static
    {
        $this->modelSources = $models;
        return $this;
    }

    public function navigationGroup(\UnitEnum|string|null $group): static
    {
        $this->navigationGroup = $group;
        return $this;
    }

    public function navigationIcon(string|\Illuminate\Contracts\Support\Htmlable|null $icon): static
    {
        $this->navigationIcon = $icon;
        return $this;
    }

    public function navigationSort(?int $sort): static
    {
        $this->navigationSort = $sort;
        return $this;
    }

    public function navigationLabel(string|\Illuminate\Contracts\Support\Htmlable|null $label): static
    {
        $this->navigationLabel = $label;
        return $this;
    }

    public function getNavigationGroup(): \UnitEnum|string|null
    {
        return $this->navigationGroup;
    }

    public function getNavigationIcon(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return $this->navigationIcon;
    }

    public function getNavigationSort(): ?int
    {
        return $this->navigationSort;
    }

    public function getNavigationLabel(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return $this->navigationLabel;
    }
}
