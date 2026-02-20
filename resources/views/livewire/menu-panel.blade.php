{{-- ============================================================
     Menu Panel Livewire Component - Sidebar
============================================================ --}}

<div>

    <div class="fmm-panel-card">

        {{-- =================== Tabs =================== --}}
        <div class="fmm-panel-tabs">
            <button
                class="fmm-panel-tab {{ $activeTab === 'custom' ? 'active' : '' }}"
                wire:click="$set('activeTab','custom')"
            >Custom Links</button>

            <button
                class="fmm-panel-tab {{ $activeTab === 'models' ? 'active' : '' }}"
                wire:click="$set('activeTab','models')"
            >Models</button>
        </div>

        {{-- =================== Panel Body =================== --}}
        <div class="fmm-panel-body">

            @if(!$menuId)
                <p style="font-size:0.8rem;color:var(--fmm-muted);text-align:center;padding:1rem 0">
                    Select or create a menu first.
                </p>

            @elseif($activeTab === 'custom')
                {{-- ----- Custom Link ----- --}}
                <div>
                    <div class="fmm-label">Title</div>
                    <input
                        type="text"
                        wire:model="customTitle"
                        placeholder="e.g. Home"
                        style="width:100%;padding:0.4rem 0.625rem;border-radius:0.375rem;border:1.5px solid var(--fmm-border);background:var(--fmm-input-bg);color:var(--fmm-text);font-size:0.8125rem;outline:none"
                    />
                </div>
                <div>
                    <div class="fmm-label">URL</div>
                    <input
                        type="text"
                        wire:model="customUrl"
                        placeholder="https://... or /"
                        style="width:100%;padding:0.4rem 0.625rem;border-radius:0.375rem;border:1.5px solid var(--fmm-border);background:var(--fmm-input-bg);color:var(--fmm-text);font-size:0.8125rem;outline:none"
                    />
                </div>
                <div>
                    <div class="fmm-label">Open in</div>
                    <select
                        wire:model="customTarget"
                        style="width:100%;padding:0.4rem 0.625rem;border-radius:0.375rem;border:1.5px solid var(--fmm-border);background:var(--fmm-input-bg);color:var(--fmm-text);font-size:0.8125rem;outline:none"
                    >
                        <option value="_self">Same Tab</option>
                        <option value="_blank">New Tab</option>
                    </select>
                </div>
                <button
                    wire:click="addCustomLink"
                    style="width:100%;padding:0.5rem;background:var(--fmm-accent);color:#fff;border:none;border-radius:0.5rem;font-size:0.8125rem;font-weight:600;cursor:pointer;transition:opacity 0.15s"
                    onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'"
                >
                    + Add to Menu
                </button>

            @elseif($activeTab === 'models')
                {{-- ----- Eloquent Model Sources ----- --}}

                @if(empty($modelSources))
                    <div class="fmm-empty" style="padding:1.5rem 0">
                        <p style="font-size:0.8rem">No model sources registered.</p>
                        <p style="font-size:0.75rem;margin-top:0.25rem">Register models via <code>->modelSources([Post::class])</code></p>
                    </div>
                @else
                    {{-- Model source selector and search --}}
                    <div>
                        <div class="fmm-label">Search</div>
                        <input
                            type="text"
                            wire:model.live.debounce.300="modelSearch"
                            placeholder="Filter records..."
                            style="width:100%;padding:0.4rem 0.625rem;border-radius:0.375rem;border:1.5px solid var(--fmm-border);background:var(--fmm-input-bg);color:var(--fmm-text);font-size:0.8125rem;outline:none"
                        />
                    </div>

                    @foreach($modelSources as $modelClass)
                        <div>
                            <div class="fmm-panel-section-header">
                                {{ class_basename($modelClass) }}
                            </div>

                            <div class="fmm-model-list" style="margin-top:0.375rem">
                                @forelse($this->getModelRecords($modelClass) as $record)
                                    @php
                                        $isUsed = in_array($record->getKey(), $usedModels[$modelClass] ?? []);
                                    @endphp
                                    <div
                                        class="fmm-model-item {{ $isUsed ? 'disabled' : '' }}"
                                        @if(!$isUsed)
                                            wire:click="addModelItem('{{ addslashes($modelClass) }}', {{ $record->getKey() }})"
                                            role="button"
                                            tabindex="0"
                                        @else
                                            style="opacity: 0.5; cursor: not-allowed;"
                                            title="Already added"
                                        @endif
                                    >
                                        <span>{{ $record->getMenuLabel() }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:0.875rem;height:0.875rem;flex-shrink:0;color:var(--fmm-muted)">
                                            @if($isUsed)
                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                            @else
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z" clip-rule="evenodd" />
                                            @endif
                                        </svg>
                                    </div>
                                @empty
                                    <p style="font-size:0.75rem;color:var(--fmm-muted);padding:0.5rem 0">No records found.</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                @endif
            @endif

        </div>
    </div>

</div>
