# Filament Menu Manager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/notebrainslab/filament-menu-manager.svg?style=flat-square)](https://packagist.org/packages/notebrainslab/filament-menu-manager)
[![License](https://img.shields.io/packagist/l/notebrainslab/filament-menu-manager.svg?style=flat-square)](https://packagist.org/packages/notebrainslab/filament-menu-manager)

A powerful **Filament v4** plugin for managing navigation menus with:

- ✅ **Multiple Locations** — Primary, Footer, Sidebar, or any custom location
- ✅ **Drag & Drop Reordering** — Powered by SortableJS with nested support
- ✅ **Button Reordering** — Up ↑ Down ↓ Indent → Outdent ← for accessibility
- ✅ **Built-in Panels** — Custom Links panel and Eloquent Model Sources panel
- ✅ **Eloquent Model Compatible** — Add Posts, Pages, or any model as menu items
- ✅ **Auto Save** — Debounced auto-save on every change (configurable)
- ✅ **Dark Theme** — Full dark mode support via CSS custom properties

---

## Requirements

| Dependency | Version |
|---|---|
| PHP | `^8.2` |
| Laravel | `^12.0` |
| Filament | `^4.0` |
| Livewire | `^3.0` |

---

## Installation

### 1. Install via Composer

```bash
composer require notebrainslab/filament-menu-manager
```

### 2. Publish and run migrations

```bash
php artisan filament-menu-manager:install
# or manually:
php artisan vendor:publish --tag="filament-menu-manager-migrations"
php artisan migrate
```

### 3. Register the plugin in your Panel Provider

```php
use NoteBrainsLab\FilamentMenuManager\FilamentMenuManagerPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugin(
            FilamentMenuManagerPlugin::make()
                ->locations([
                    'primary' => 'Primary Navigation',
                    'footer'  => 'Footer Links',
                ])
        );
}
```

---

## Configuration

Publish the config file (Optional):

```bash
php artisan vendor:publish --tag="filament-menu-manager-config"
```

Publish the resource files (Optional):

```bash
php artisan vendor:publish --tag="filament-menu-manager-views"
```

---

## Plugin API (Fluent)

```php
FilamentMenuManagerPlugin::make()
    ->locations(['primary' => 'Primary', 'footer' => 'Footer'])
    ->modelSources([\App\Models\Post::class, \App\Models\Page::class])
    ->navigationGroup('Content')
    ->navigationIcon('heroicon-o-bars-3')
    ->navigationSort(10)
    ->navigationLabel('Menus');
```

---

## Eloquent Model Sources

To make an Eloquent model selectable in the **Models panel**, add the trait:

```php
use NoteBrainsLab\FilamentMenuManager\Concerns\HasMenuItems;

class Post extends Model
{
    use HasMenuItems;

    // Optional: override the defaults
    public function getMenuLabel(): string  { return $this->title; }
    public function getMenuUrl(): string    { return route('posts.show', $this); }
    public function getMenuTarget(): string { return '_self'; }
    public function getMenuIcon(): ?string  { return 'heroicon-o-document'; }
}
```

Then register the model in the plugin:

```php
FilamentMenuManagerPlugin::make()
    ->modelSources([\App\Models\Post::class])
```

---

## Render Menus in Blade

```blade
@php
    $manager = app(\NoteBrainsLab\FilamentMenuManager\MenuManager::class);
    $menus   = $manager->menusForLocation('primary');
    $menu    = $menus->first();
    $tree    = $menu?->getTree() ?? [];
@endphp

@foreach($tree as $item)
    <a href="{{ $item['url'] }}" target="{{ $item['target'] }}">{{ $item['title'] }}</a>
    @if(!empty($item['children']))
        {{-- render children --}}
    @endif
@endforeach
```

---

## Testing

```bash
composer test
```

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

MIT License. See [LICENSE](LICENSE).
