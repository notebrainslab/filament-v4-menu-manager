# Changelog

All notable changes to `filament-menu-manager` will be documented in this file.

## [2.0.0] — 2026-02-21

### Added
- **Filament v5 Support** — Added compatibility with the newly released Filament v5 core while maintaining full support for v4.
- **Livewire v4 Support** — Added compatibility with Livewire v4 to ensure smooth integration with the latest Laravel environments.

## [1.0.0] — 2024-02-19

### Added

- **Multiple Menu Locations** — Register any number of named locations (`primary`, `footer`, etc.) via plugin fluent API or config file. Locations are persisted to the database and synced automatically on boot.
- **Drag & Drop Reorder** — Full SortableJS-powered nested drag-and-drop with cross-list nesting support. Tree order is persisted via Livewire after each drag action.
- **Button Reorder** — Up ↑, Down ↓, Indent →, Outdent ← action buttons as a fallback for accessibility or users who prefer not to drag.
- **Inline Item Editing** — Click the edit button on any item to open an inline form to update the title, URL, target, and visibility without leaving the page.
- **Visibility Toggle** — Enable/disable individual menu items with a single click. Disabled items are visually dimmed and marked with a "Hidden" badge.
- **Built-in Custom Links Panel** — Sidebar panel to add custom link items (title + URL + target) directly to the active menu.
- **Eloquent Model Sources Panel** — Register any Eloquent model using the `HasMenuItems` trait. The panel lists model records with a live search filter.
- **Polymorphic Linkable Relation** — `MenuItem` supports `linkable_type` / `linkable_id` morphable relation to any model for resolved URLs.
- **Auto Save** — Every change triggers an auto-save with configurable debounce (default 800ms). A green "Saved" flash indicator confirms the save.
- **Dark Theme** — Full dark mode support via CSS custom properties. Automatically responds to Filament's `.dark` class on `<html>`.
- **Multiple Menus per Location** — Create and switch between multiple menus within a single location using the menu switcher tab bar.
- **Create / Delete Menu Actions** — Header actions to create a new menu (with location selector) or delete the active menu with confirmation.
- **Install Command** — `php artisan filament-menu-manager:install` publishes config/migrations and optionally runs migrations.
- **Configurable Models** — All three models (`MenuLocation`, `Menu`, `MenuItem`) can be swapped out via config for your own extended versions.
- **Pest Test Suite** — 6 passing tests covering location creation, uniqueness, relationships, tree building, URL resolution, and enabled toggling.
