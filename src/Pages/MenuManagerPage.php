<?php

namespace NoteBrainsLab\FilamentMenuManager\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use NoteBrainsLab\FilamentMenuManager\MenuManager;
use NoteBrainsLab\FilamentMenuManager\Models\Menu;
use NoteBrainsLab\FilamentMenuManager\Models\MenuLocation;

class MenuManagerPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament-menu-manager::pages.menu-manager';

    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationIcon  = 'heroicon-o-bars-3';
    protected static ?string $navigationLabel = 'Menu Manager';
    protected static ?int    $navigationSort  = 99;

    public static function getNavigationGroup(): ?string
    {
        return \NoteBrainsLab\FilamentMenuManager\FilamentMenuManagerPlugin::get()->getNavigationGroup() ?? static::$navigationGroup;
    }

    public static function getNavigationIcon(): ?string
    {
        return \NoteBrainsLab\FilamentMenuManager\FilamentMenuManagerPlugin::get()->getNavigationIcon() ?? static::$navigationIcon;
    }

    public static function getNavigationLabel(): string
    {
        return \NoteBrainsLab\FilamentMenuManager\FilamentMenuManagerPlugin::get()->getNavigationLabel() ?? static::$navigationLabel;
    }

    public static function getNavigationSort(): ?int
    {
        return \NoteBrainsLab\FilamentMenuManager\FilamentMenuManagerPlugin::get()->getNavigationSort() ?? static::$navigationSort;
    }

    // -------------------------------------------------------------------------
    // State
    // -------------------------------------------------------------------------

    public ?int    $activeLocationId = null;
    public ?int    $activeMenuId     = null;
    public string  $activeLocationHandle = '';

    // -------------------------------------------------------------------------
    // Lifecycle
    // -------------------------------------------------------------------------

    public function mount(): void
    {
        $locations = $this->getLocations();

        if ($locations->isNotEmpty()) {
            $first = $locations->first();
            $this->activeLocationId     = $first->id;
            $this->activeLocationHandle = $first->handle;

            $menus = $this->getMenusForActiveLocation();
            if ($menus->isNotEmpty()) {
                $this->activeMenuId = $menus->first()->id;
            }
        }
    }

    // -------------------------------------------------------------------------
    // Computed helpers
    // -------------------------------------------------------------------------

    public function getMenuManager(): MenuManager
    {
        return app(MenuManager::class);
    }

    public function getLocations(): Collection
    {
        return $this->getMenuManager()->allLocations();
    }

    public function getMenusForActiveLocation(): Collection
    {
        if (! $this->activeLocationId) {
            return collect();
        }

        $menuModel = config('filament-menu-manager.models.menu', Menu::class);
        return $menuModel::where('menu_location_id', $this->activeLocationId)
                         ->orderBy('name')
                         ->get();
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------

    public function switchLocation(int $locationId): void
    {
        $locationModel = config('filament-menu-manager.models.menu_location', MenuLocation::class);
        $location = $locationModel::find($locationId);

        if ($location) {
            $this->activeLocationId     = $location->id;
            $this->activeLocationHandle = $location->handle;
            $this->activeMenuId         = null;

            $menus = $this->getMenusForActiveLocation();
            if ($menus->isNotEmpty()) {
                $this->activeMenuId = $menus->first()->id;
            }
        }
    }

    public function switchMenu(int $menuId): void
    {
        $this->activeMenuId = $menuId;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createMenu')
                ->label('New Menu')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->form([
                    Select::make('menu_location_id')
                        ->label('Location')
                        ->options(
                            fn () => $this->getLocations()->pluck('name', 'id')->toArray()
                        )
                        ->default(fn () => $this->activeLocationId)
                        ->required(),
                    TextInput::make('name')
                        ->label('Menu Name')
                        ->required()
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    $menuModel = config('filament-menu-manager.models.menu', Menu::class);
                    $menu = $menuModel::create($data);
                    $this->activeMenuId = $menu->id;
                    Notification::make('menu_created')
                        ->title('Menu created')
                        ->success()
                        ->send();
                }),

            Action::make('deleteMenu')
                ->label('Delete Menu')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn () => $this->activeMenuId !== null)
                ->action(function (): void {
                    if ($this->activeMenuId) {
                        $menuModel = config('filament-menu-manager.models.menu', Menu::class);
                        $menuModel::destroy($this->activeMenuId);
                        $this->activeMenuId = null;

                        $menus = $this->getMenusForActiveLocation();
                        if ($menus->isNotEmpty()) {
                            $this->activeMenuId = $menus->first()->id;
                        }

                        Notification::make('menu_deleted')
                            ->title('Menu deleted')
                            ->success()
                            ->send();
                    }
                }),
        ];
    }
}
