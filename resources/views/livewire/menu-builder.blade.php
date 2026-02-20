{{-- ============================================================
     Menu Builder Livewire Component
============================================================ --}}

<div>

@if(!$hasMenu)
    <div class="fmm-builder-card">
        <div class="fmm-empty">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
            </svg>
            <p class="text-sm font-semibold">No menu selected</p>
            <p class="text-xs">Create or select a menu above to start building.</p>
        </div>
    </div>
@else
    <div class="fmm-builder-card">

        {{-- Header --}}
        <div class="fmm-builder-header">
            <span>Menu Items</span>

            @if($autoSave)
                <span style="font-size:0.7rem;font-weight:500;color:var(--fmm-muted);">
                    ● Auto-save enabled
                </span>
            @endif
        </div>

        {{-- Sortable Tree --}}
        <div
            class="fmm-root-list fmm-nested-sortable"
            id="fmm-root-list"
            x-data="menuSortable($wire)"
        >
            @forelse($items as $item)
                @include('filament-menu-manager::components.menu-item', ['item' => $item, 'depth' => 0])
            @empty
                <div class="fmm-empty" style="padding:2rem 1rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <p class="text-xs">Add items from the panel on the right.</p>
                </div>
            @endforelse
        </div>

    </div>
@endif

    {{-- Delete Confirmation Modal --}}
    @if($deletingItemId)
        @teleport('body')
            <div 
                class="fmm-modal-overlay"
                wire:click.self="cancelRemoval"
            >
                <div 
                    class="fmm-modal-content"
                    role="dialog"
                    aria-modal="true"
                >
                    <div style="padding: 1.5rem; display: flex; gap: 1rem; align-items: flex-start;">
                        <div style="flex-shrink: 0; width: 2.5rem; height: 2.5rem; border-radius: 9999px; background: rgb(254 226 226); display: flex; align-items: center; justify-content: center; color: rgb(220 38 38);">
                            <!-- Heroicon outline/exclamation-triangle -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 1.5rem; height: 1.5rem;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="font-size: 1rem; font-weight: 600; color: var(--fmm-text); margin: 0;">
                                Delete Menu Item
                            </h3>
                            <div style="margin-top: 0.5rem;">
                                <p style="font-size: 0.875rem; color: var(--fmm-muted);">
                                    Are you sure you want to remove this item? This action cannot be undone and will also remove any children items.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div style="padding: 1rem 1.5rem; background: var(--fmm-handle-bg); border-top: 1px solid var(--fmm-border); display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <button 
                            type="button" 
                            wire:click="cancelRemoval"
                            class="fmm-btn fmm-btn-secondary"
                        >
                            Cancel
                        </button>
                        <button 
                            type="button" 
                            wire:click="removeItem({{ $deletingItemId }})"
                            class="fmm-btn fmm-btn-danger"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        @endteleport
    @endif

</div>
