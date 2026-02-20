<?php

namespace NoteBrainsLab\FilamentMenuManager\Livewire;

use Livewire\Component;
use NoteBrainsLab\FilamentMenuManager\MenuManager;
use NoteBrainsLab\FilamentMenuManager\Models\MenuItem;

class MenuPanel extends Component
{
    // -------------------------------------------------------------------------
    // Props
    // -------------------------------------------------------------------------

    public ?int    $menuId        = null;
    public string  $locationHandle = '';

    // -------------------------------------------------------------------------
    // State
    // -------------------------------------------------------------------------

    public string  $activeTab     = 'custom';  // custom | pages | models
    public string  $customTitle   = '';
    public string  $customUrl     = '';
    public string  $customTarget  = '_self';
    public string  $modelSearch   = '';
    
    // Track used model items: [ModelClass => [id1, id2, ...]]
    public array   $usedModels    = [];

    // -------------------------------------------------------------------------
    // Listeners
    // -------------------------------------------------------------------------

    protected $listeners = [
        'menuIdChanged'        => 'onMenuChanged',
        'menu-content-updated' => 'refreshUsedModels',
    ];

    public function onMenuChanged(int $menuId): void
    {
        $this->menuId = $menuId;
        $this->refreshUsedModels();
    }

    public function mount(): void
    {
        if ($this->menuId) {
            $this->refreshUsedModels();
        }
    }

    public function refreshUsedModels(): void
    {
        if (! $this->menuId) {
            $this->usedModels = [];
            return;
        }

        $itemModel = config('filament-menu-manager.models.menu_item', MenuItem::class);
        
        $this->usedModels = $itemModel::where('menu_id', $this->menuId)
            ->whereNotNull('linkable_type')
            ->whereNotNull('linkable_id')
            ->get()
            ->groupBy('linkable_type')
            ->map(fn ($items) => $items->pluck('linkable_id')->all())
            ->toArray();
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------

    public function addCustomLink(): void
    {
        if (empty($this->customTitle)) return;

        $this->dispatch('menuItemAdded', [
            'title'  => $this->customTitle,
            'url'    => $this->customUrl ?: '#',
            'target' => $this->customTarget,
            'type'   => 'custom',
        ]);

        $this->customTitle  = '';
        $this->customUrl    = '';
        $this->customTarget = '_self';
    }

    public function addModelItem(string $modelClass, int $modelId): void
    {
        /** @var \NoteBrainsLab\FilamentMenuManager\Contracts\MenuItemSource $model */
        $model = $modelClass::find($modelId);
        if (! $model) return;

        if (in_array($modelId, $this->usedModels[$modelClass] ?? [])) {
            return;
        }

        $this->dispatch('menuItemAdded', [
            'title'         => $model->getMenuLabel(),
            'url'           => $model->getMenuUrl(),
            'target'        => $model->getMenuTarget(),
            'icon'          => $model->getMenuIcon(),
            'type'          => 'model',
            'linkable_type' => $modelClass,
            'linkable_id'   => $modelId,
        ]);
    }

    // -------------------------------------------------------------------------
    // Computed helpers
    // -------------------------------------------------------------------------

    public function getModelSources(): array
    {
        return app(MenuManager::class)->getModelSources();
    }

    public function getModelRecords(string $modelClass): \Illuminate\Support\Collection
    {
        if (! class_exists($modelClass)) return collect();

        $query = $modelClass::query();
        if ($this->modelSearch) {
            // Search in name/title if the column exists
            $table   = (new $modelClass)->getTable();
            $columns = \Illuminate\Support\Facades\Schema::getColumnListing($table);
            $search  = $this->modelSearch;

            $query->where(function ($q) use ($columns, $search) {
                foreach (['name', 'title', 'label'] as $col) {
                    if (in_array($col, $columns)) {
                        $q->orWhere($col, 'like', "%{$search}%");
                    }
                }
            });
        }

        return $query->limit(50)->get();
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render()
    {
        return view('filament-menu-manager::livewire.menu-panel', [
            'modelSources' => $this->getModelSources(),
        ]);
    }
}
