<?php

namespace NoteBrainsLab\FilamentMenuManager;

use NoteBrainsLab\FilamentMenuManager\Models\Menu;
use NoteBrainsLab\FilamentMenuManager\Models\MenuLocation;
use NoteBrainsLab\FilamentMenuManager\Models\MenuItem;

class MenuManager
{
    protected array $locations = [];
    protected array $modelSources = [];

    /**
     * Register menu locations (handle => name).
     */
    public function registerLocations(array $locations): void
    {
        foreach ($locations as $handle => $name) {
            $key    = is_int($handle) ? $name : $handle;
            $label  = is_int($handle) ? ucfirst($name) : $name;
            $this->locations[$key] = $label;
        }
    }

    /**
     * Register Eloquent model classes as item sources.
     */
    public function registerModelSources(array $models): void
    {
        $this->modelSources = array_merge($this->modelSources, $models);
    }

    public function getLocations(): array
    {
        return $this->locations;
    }

    public function getModelSources(): array
    {
        return $this->modelSources;
    }

    /**
     * Sync configured locations into the DB (create if missing, update if exists, delete if removed from config).
     */
    public function syncLocations(): void
    {
        $locationModel = config('filament-menu-manager.models.menu_location', MenuLocation::class);
        $menuModel     = config('filament-menu-manager.models.menu', Menu::class);
        $itemModel     = config('filament-menu-manager.models.menu_item', MenuItem::class);

        $handles = array_keys($this->locations);

        // Update or create locations from config
        foreach ($this->locations as $handle => $name) {
            $locationModel::updateOrCreate(
                ['handle' => $handle],
                ['name'   => $name]
            );
        }

        // Get deprecated locations
        $deprecated = $locationModel::whereNotIn('handle', $handles)->get();

        if ($deprecated->isNotEmpty()) {
            $deprecatedIds = $deprecated->pluck('id')->toArray();
            
            // 1. Find all menus belonging to these locations
            $menuIds = $menuModel::whereIn('menu_location_id', $deprecatedIds)->pluck('id')->toArray();
            
            if (!empty($menuIds)) {
                // 2. Delete all items belonging to these menus first
                // (This avoids the parent_id nullOnDelete race condition)
                $itemModel::whereIn('menu_id', $menuIds)->delete();
                
                // 3. Delete the menus
                $menuModel::whereIn('id', $menuIds)->delete();
            }

            // 4. Finally delete the locations
            $locationModel::whereIn('id', $deprecatedIds)->delete();
        }
    }

    /**
     * Get all DB-persisted locations.
     */
    public function allLocations()
    {
        $locationModel = config('filament-menu-manager.models.menu_location', MenuLocation::class);
        return $locationModel::orderBy('name')->get();
    }

    /**
     * Retrieve menus for a given location handle.
     */
    public function menusForLocation(string $handle)
    {
        $locationModel = config('filament-menu-manager.models.menu_location', MenuLocation::class);
        $location = $locationModel::where('handle', $handle)->first();

        return $location ? $location->menus()->orderBy('name')->get() : collect();
    }

    /**
     * Save tree order from a nested array (from SortableJS).
     * Expected format: [['id' => 1, 'children' => [['id' => 2], ...]], ...]
     */
    public function saveTree(int $menuId, array $tree, ?int $parentId = null, int &$order = 0): void
    {
        $itemModel = config('filament-menu-manager.models.menu_item', MenuItem::class);

        foreach ($tree as $node) {
            $order++;
            $itemModel::where('id', $node['id'])->update([
                'parent_id' => $parentId,
                'order'     => $order,
            ]);

            if (! empty($node['children'])) {
                $this->saveTree($menuId, $node['children'], $node['id'], $order);
            }
        }
    }
}
