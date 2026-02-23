<x-filament-panels::page>
    <x-filament::card>
        {{-- ============================================================
             Location Bar
        ============================================================ --}}
        <div class="fmm-location-bar">
            @foreach ($this->getLocations() as $location)
                <button
                    class="fmm-location-btn {{ $activeLocationId === $location->id ? 'active' : '' }}"
                    wire:click="switchLocation({{ $location->id }})"
                >
                    {{ $location->name }}
                </button>
            @endforeach
        </div>

        {{-- ============================================================
             Menu Switcher
        ============================================================ --}}
        @if($this->getLocations()->isNotEmpty())
            <div class="fmm-location-bar" style="margin-bottom:1.5rem">
                @foreach ($this->getMenusForActiveLocation() as $menu)
                    <button
                        class="fmm-menu-btn {{ $activeMenuId === $menu->id ? 'active' : '' }}"
                        wire:click="switchMenu({{ $menu->id }})"
                    >
                        {{ $menu->name }}
                    </button>
                @endforeach

                @if($this->getMenusForActiveLocation()->isEmpty())
                    <p class="text-sm text-gray-400 dark:text-gray-500">
                        No menus yet — create one using the button above.
                    </p>
                @endif
            </div>
        @endif

        {{-- ============================================================
             Main 2-column layout (builder + panel)
        ============================================================ --}}
        <div class="fmm-manager">

            {{-- Builder --}}
            @livewire(
                'filament-menu-manager.menu-builder',
                ['menuId' => $activeMenuId, 'locationHandle' => $activeLocationHandle],
                key('builder-' . $activeMenuId)
            )

            {{-- Panel --}}
            @livewire(
                'filament-menu-manager.menu-panel',
                ['menuId' => $activeMenuId, 'locationHandle' => $activeLocationHandle],
                key('panel-' . $activeMenuId)
            )

        </div>

        {{-- ============================================================
             Auto-save flash indicator
        ============================================================ --}}
        <div id="fmm-autosave-flash">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:1rem;height:1rem">
                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
            </svg>
            Saved
        </div>
    </x-filament::card>
</x-filament-panels::page>