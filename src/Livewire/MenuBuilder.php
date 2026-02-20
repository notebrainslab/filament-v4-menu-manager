<?php

namespace NoteBrainsLab\FilamentMenuManager\Livewire;

use Livewire\Component;
use NoteBrainsLab\FilamentMenuManager\MenuManager;
use NoteBrainsLab\FilamentMenuManager\Models\Menu;
use NoteBrainsLab\FilamentMenuManager\Models\MenuItem;

class MenuBuilder extends Component
{
    // -------------------------------------------------------------------------
    // Props
    // -------------------------------------------------------------------------

    public ?int   $menuId        = null;
    public string $locationHandle = '';

    // -------------------------------------------------------------------------
    // State
    // -------------------------------------------------------------------------

    public array $items = [];  // nested tree for the view

    // Inline editing state
    public ?int   $editingItemId  = null;
    public string $editingTitle   = '';
    public string $editingUrl     = '';
    public string $editingTarget  = '_self';
    public bool   $editingEnabled = true;

    // UI state
    public bool   $autoSave       = true;
    public bool   $isDirty        = false;

    // -------------------------------------------------------------------------
    // Lifecycle & events
    // -------------------------------------------------------------------------

    public function mount(?int $menuId = null, string $locationHandle = ''): void
    {
        $this->menuId         = $menuId;
        $this->locationHandle = $locationHandle;
        $this->autoSave       = config('filament-menu-manager.auto_save', true);
        $this->loadItems();
    }

    public function updatedMenuId(): void
    {
        $this->loadItems();
        $this->editingItemId = null;
    }

    protected function loadItems(): void
    {
        if (! $this->menuId) {
            $this->items = [];
            return;
        }

        $menuModel = config('filament-menu-manager.models.menu', Menu::class);
        $menu = $menuModel::find($this->menuId);
        $this->items = $menu ? $menu->getTree() : [];
    }

    // -------------------------------------------------------------------------
    // Listeners
    // -------------------------------------------------------------------------

    protected $listeners = [
        'menuItemAdded'   => 'addItem',
        'menuIdChanged'   => 'changeMenu',
    ];

    public function changeMenu(int $menuId): void
    {
        $this->menuId = $menuId;
        $this->loadItems();
        $this->editingItemId = null;
    }

    // -------------------------------------------------------------------------
    // Drag & drop order save
    // -------------------------------------------------------------------------

    /**
     * Called from JS after drag & drop — receives the new sorted tree.
     * Tree format: [['id' => 1, 'children' => [['id' => 3]]], ['id' => 2]]
     */
    public function updateOrder(array $tree): void
    {
        if (! $this->menuId) return;

        $order = 0;
        app(MenuManager::class)->saveTree($this->menuId, $tree, null, $order);
        $this->loadItems();
        $this->isDirty = false;
        $this->dispatch('menu-saved');
    }

    // -------------------------------------------------------------------------
    // Button-based reorder
    // -------------------------------------------------------------------------

    public function moveUp(int $itemId): void
    {
        $this->shiftItem($itemId, 'up');
    }

    public function moveDown(int $itemId): void
    {
        $this->shiftItem($itemId, 'down');
    }

    public function indentItem(int $itemId): void
    {
        $itemModel = config('filament-menu-manager.models.menu_item', MenuItem::class);
        $item      = $itemModel::find($itemId);
        if (! $item) return;

        // Find the sibling immediately before this item at the same level
        $sibling = $itemModel::where('menu_id', $item->menu_id)
            ->where('parent_id', $item->parent_id)
            ->where('order', '<', $item->order)
            ->orderByDesc('order')
            ->first();

        if ($sibling) {
            $maxOrder  = $itemModel::where('menu_id', $item->menu_id)
                ->where('parent_id', $sibling->id)
                ->max('order') ?? 0;

            $item->update(['parent_id' => $sibling->id, 'order' => $maxOrder + 1]);
            $this->loadItems();
        }
    }

    public function outdentItem(int $itemId): void
    {
        $itemModel = config('filament-menu-manager.models.menu_item', MenuItem::class);
        $item      = $itemModel::find($itemId);
        if (! $item || ! $item->parent_id) return;

        $parent   = $item->parent;
        $maxOrder = $itemModel::where('menu_id', $item->menu_id)
            ->where('parent_id', $parent->parent_id)
            ->max('order') ?? 0;

        $item->update(['parent_id' => $parent->parent_id, 'order' => $maxOrder + 1]);
        $this->loadItems();
    }

    protected function shiftItem(int $itemId, string $direction): void
    {
        $itemModel = config('filament-menu-manager.models.menu_item', MenuItem::class);
        $item      = $itemModel::find($itemId);
        if (! $item) return;

        $sibling = $direction === 'up'
            ? $itemModel::where('menu_id', $item->menu_id)
                ->where('parent_id', $item->parent_id)
                ->where('order', '<', $item->order)
                ->orderByDesc('order')
                ->first()
            : $itemModel::where('menu_id', $item->menu_id)
                ->where('parent_id', $item->parent_id)
                ->where('order', '>', $item->order)
                ->orderBy('order')
                ->first();

        if ($sibling) {
            [$item->order, $sibling->order] = [$sibling->order, $item->order];
            $item->save();
            $sibling->save();
            $this->loadItems();
        }
    }

    // -------------------------------------------------------------------------
    // Add / Remove / Edit items
    // -------------------------------------------------------------------------

    public function addItem(array $data): void
    {
        if (! $this->menuId) return;

        $itemModel = config('filament-menu-manager.models.menu_item', MenuItem::class);

        $maxOrder = $itemModel::where('menu_id', $this->menuId)
            ->whereNull('parent_id')
            ->max('order') ?? 0;

        $itemModel::create(array_merge([
            'menu_id'   => $this->menuId,
            'parent_id' => null,
            'order'     => $maxOrder + 1,
            'target'    => '_self',
            'enabled'   => true,
            'type'      => 'custom',
        ], $data));

        $this->loadItems();

        if ($this->autoSave) {
            $this->dispatch('menu-saved');
        }
        $this->dispatch('menu-content-updated');
    }

    // -------------------------------------------------------------------------
    // Delete Confirmation State
    // -------------------------------------------------------------------------
    public ?int $deletingItemId = null;

    public function confirmRemoval(int $itemId): void
    {
        $this->deletingItemId = $itemId;
    }

    public function cancelRemoval(): void
    {
        $this->deletingItemId = null;
    }

    public function removeItem(int $itemId): void
    {
        $itemModel = config('filament-menu-manager.models.menu_item', MenuItem::class);
        // Recursively re-parent children before deletion
        $itemModel::where('parent_id', $itemId)->update(['parent_id' => null]);
        $itemModel::destroy($itemId);
        $this->loadItems();
        $this->dispatch('menu-content-updated');
        $this->deletingItemId = null;
    }

    public function startEdit(int $itemId): void
    {
        $itemModel = config('filament-menu-manager.models.menu_item', MenuItem::class);
        $item = $itemModel::find($itemId);

        if ($item) {
            $this->editingItemId  = $itemId;
            $this->editingTitle   = $item->title;
            $this->editingUrl     = $item->url ?? '';
            $this->editingTarget  = $item->target ?? '_self';
            $this->editingEnabled = (bool) $item->enabled;
        }
    }

    public function cancelEdit(): void
    {
        $this->editingItemId = null;
    }

    public function saveEdit(): void
    {
        if (! $this->editingItemId) return;

        $itemModel = config('filament-menu-manager.models.menu_item', MenuItem::class);
        $itemModel::where('id', $this->editingItemId)->update([
            'title'   => $this->editingTitle,
            'url'     => $this->editingUrl,
            'target'  => $this->editingTarget,
            'enabled' => $this->editingEnabled,
        ]);

        $this->editingItemId = null;
        $this->loadItems();

        if ($this->autoSave) {
            $this->dispatch('menu-saved');
        }
    }

    public function toggleEnabled(int $itemId): void
    {
        $itemModel = config('filament-menu-manager.models.menu_item', MenuItem::class);
        $item = $itemModel::find($itemId);
        if ($item) {
            $item->update(['enabled' => ! $item->enabled]);
            $this->loadItems();
        }
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render()
    {
        return view('filament-menu-manager::livewire.menu-builder', [
            'items'       => $this->items,
            'hasMenu'     => $this->menuId !== null,
            'debounce'    => config('filament-menu-manager.auto_save_debounce', 800),
        ]);
    }
}
