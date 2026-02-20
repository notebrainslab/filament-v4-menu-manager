{{-- ============================================================
     Recursive Menu Item Component
     Usage: @include('filament-menu-manager::components.menu-item', compact('item','depth'))
============================================================ --}}

<div class="fmm-item-row" data-id="{{ $item['id'] }}">

    {{-- Item Card --}}
    <div class="fmm-item-card {{ !$item['enabled'] ? 'disabled-item' : '' }}">

        {{-- Drag Handle --}}
        <span class="fmm-drag-handle" title="Drag to reorder">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:1rem;height:1rem">
                <path fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z" clip-rule="evenodd" />
            </svg>
        </span>

        {{-- Body --}}
        <div class="fmm-item-body">
            <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap">
                <span class="fmm-item-title">{{ $item['title'] }}</span>
                <span class="fmm-item-badge">{{ $item['type'] ?? 'custom' }}</span>
                @if(!$item['enabled'])
                    <span class="fmm-item-badge" style="background:rgb(254 226 226);color:rgb(185 28 28);">Hidden</span>
                @endif
            </div>
            <span class="fmm-item-url">{{ $item['url'] ?? '#' }}</span>
        </div>

        {{-- Action Buttons --}}
        <div class="fmm-item-actions">

            {{-- Move Up --}}
            <button class="fmm-action-btn" wire:click="moveUp({{ $item['id'] }})" title="Move up">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:0.875rem;height:0.875rem">
                    <path fill-rule="evenodd" d="M9.47 6.47a.75.75 0 011.06 0l4.25 4.25a.75.75 0 11-1.06 1.06L10 8.06l-3.72 3.72a.75.75 0 01-1.06-1.06l4.25-4.25z" clip-rule="evenodd" />
                </svg>
            </button>

            {{-- Move Down --}}
            <button class="fmm-action-btn" wire:click="moveDown({{ $item['id'] }})" title="Move down">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:0.875rem;height:0.875rem">
                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 000 1.06l4.25 4.25a.75.75 0 001.06 0l4.25-4.25a.75.75 0 00-1.06-1.06L10 11.94 6.28 8.22a.75.75 0 00-1.06 0z" clip-rule="evenodd" />
                </svg>
            </button>

            {{-- Indent (→) --}}
            <button class="fmm-action-btn" wire:click="indentItem({{ $item['id'] }})" title="Indent (make child)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:0.875rem;height:0.875rem">
                    <path d="M3 4.25a.75.75 0 01.75-.75h6.5a.75.75 0 010 1.5h-6.5A.75.75 0 013 4.25zm0 5a.75.75 0 01.75-.75H7.5a.75.75 0 010 1.5H3.75A.75.75 0 013 9.25zm0 5a.75.75 0 01.75-.75H7.5a.75.75 0 010 1.5H3.75A.75.75 0 013 14.25zm9.78-6.47a.75.75 0 011.06 0l3.25 3.25a.75.75 0 010 1.06l-3.25 3.25a.75.75 0 01-1.06-1.06l2.72-2.72-2.72-2.72a.75.75 0 010-1.06z" />
                </svg>
            </button>

            {{-- Outdent (←) --}}
            <button class="fmm-action-btn" wire:click="outdentItem({{ $item['id'] }})" title="Outdent (make parent)">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:0.875rem;height:0.875rem">
                    <path d="M3 4.25a.75.75 0 01.75-.75h12.5a.75.75 0 010 1.5H3.75A.75.75 0 013 4.25zm0 5a.75.75 0 01.75-.75H7.5a.75.75 0 010 1.5H3.75A.75.75 0 013 9.25zm0 5a.75.75 0 01.75-.75H7.5a.75.75 0 010 1.5H3.75A.75.75 0 013 14.25zm12.78-6.47a.75.75 0 010 1.06l-2.72 2.72 2.72 2.72a.75.75 0 01-1.06 1.06l-3.25-3.25a.75.75 0 010-1.06l3.25-3.25a.75.75 0 011.06 0z" />
                </svg>
            </button>

            {{-- Toggle visibility --}}
            <button class="fmm-action-btn" wire:click="toggleEnabled({{ $item['id'] }})" title="{{ $item['enabled'] ? 'Disable' : 'Enable' }}">
                @if($item['enabled'])
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:0.875rem;height:0.875rem">
                        <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />
                        <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:0.875rem;height:0.875rem;opacity:0.4">
                        <path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 00-1.06 1.06l14.5 14.5a.75.75 0 101.06-1.06l-1.745-1.745a10.029 10.029 0 003.3-4.38 1.651 1.651 0 000-1.185A10.004 10.004 0 009.999 3a9.956 9.956 0 00-4.744 1.194L3.28 2.22zM7.752 6.69l1.092 1.092a2.5 2.5 0 013.374 3.373l1.091 1.092a4 4 0 00-5.557-5.557z" clip-rule="evenodd" />
                        <path d="M10.748 13.93l2.523 2.523a10.003 10.003 0 01-3.27.547c-4.258 0-7.894-2.66-9.337-6.41a1.651 1.651 0 010-1.186A10.007 10.007 0 012.839 6.02L6.07 9.252a4 4 0 004.678 4.678z" />
                    </svg>
                @endif
            </button>

            {{-- Edit --}}
            <button class="fmm-action-btn" wire:click="startEdit({{ $item['id'] }})" title="Edit">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:0.875rem;height:0.875rem">
                    <path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" />
                    <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z" />
                </svg>
            </button>

            {{-- Delete --}}
            <button class="fmm-action-btn danger" wire:click="confirmRemoval({{ $item['id'] }})" title="Remove">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:0.875rem;height:0.875rem">
                    <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.52.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                </svg>
            </button>

        </div>
    </div>

    {{-- Inline Edit Form --}}
    @if(isset($editingItemId) && $editingItemId == $item['id'])
        <div class="fmm-edit-form" style="margin-top:0.375rem">
            <div>
                <div class="fmm-label">Title</div>
                <input type="text" wire:model="editingTitle" placeholder="Menu item title" />
            </div>
            <div class="fmm-edit-row">
                <div style="flex:2">
                    <div class="fmm-label">URL</div>
                    <input type="text" wire:model="editingUrl" placeholder="https://..." />
                </div>
                <div style="flex:1">
                    <div class="fmm-label">Target</div>
                    <select wire:model="editingTarget">
                        <option value="_self">Same Tab</option>
                        <option value="_blank">New Tab</option>
                    </select>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:0.5rem">
                <input type="checkbox" wire:model="editingEnabled" id="enabled-{{ $item['id'] }}" style="width:auto" />
                <label for="enabled-{{ $item['id'] }}" class="fmm-label" style="margin:0;cursor:pointer">Visible</label>
            </div>
            <div class="fmm-edit-actions">
                <button
                    class="fmm-action-btn"
                    wire:click="cancelEdit"
                    style="width:auto;padding:0.3rem 0.75rem;font-size:0.8rem;border:1.5px solid var(--fmm-border)"
                >Cancel</button>
                <button
                    wire:click="saveEdit"
                    style="padding:0.3rem 0.875rem;font-size:0.8rem;font-weight:600;background:var(--fmm-accent);color:#fff;border:none;border-radius:0.375rem;cursor:pointer"
                >Save</button>
            </div>
        </div>
    @endif

    {{-- Children (recursive) --}}
    @if(!empty($item['children']))
        <div class="fmm-nested-list fmm-nested-sortable" x-data="menuSortable($wire)">
            @foreach($item['children'] as $child)
                @include('filament-menu-manager::components.menu-item', ['item' => $child, 'depth' => $depth + 1])
            @endforeach
        </div>
    @else
        {{-- Empty sortable drop zone for nesting --}}
        <div class="fmm-nested-list fmm-nested-sortable" x-data="menuSortable($wire)"></div>
    @endif

</div>
