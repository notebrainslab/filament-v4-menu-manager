import Sortable from 'sortablejs';

// Expose Sortable globally so it can be used in inline scripts if needed
window.Sortable = Sortable;

// ============================================================
// Filament Menu Manager — Frontend Script
// ============================================================

// Helper: extract ordered tree from the DOM
function extractTree(container) {
    const result = [];
    const children = container.querySelectorAll(':scope > .fmm-item-row');
    children.forEach(el => {
        const id = parseInt(el.dataset.id, 10);
        if (!id) return;

        const nestedList = el.querySelector(':scope > .fmm-nested-list');
        const node = {
            id: id,
            children: nestedList ? extractTree(nestedList) : []
        };
        result.push(node);
    });
    return result;
}

// Alpine.js component for a single sortable level
function menuSortable(wire) {
    return {
        sortable: null,

        init() {
            this.$nextTick(() => this.initSortable());
        },

        initSortable() {
            const el = this.$el;
            if (!el || el._sortable) return;

            this.sortable = Sortable.create(el, {
                group: {
                    name: 'menu-items',
                    pull: true,
                    put: true,
                },
                animation: 150,
                handle: '.fmm-drag-handle',
                ghostClass: 'fmm-ghost',
                chosenClass: 'fmm-chosen',
                dragClass: 'fmm-dragging',
                fallbackOnBody: true,
                swapThreshold: 0.65,

                onEnd: () => {
                    // Walk the root list to build the full tree
                    const rootContainer = document.getElementById('fmm-root-list') || el.closest('.fmm-root-list') || el;
                    const tree = extractTree(rootContainer);

                    // Call the Livewire method
                    if (wire) {
                        wire.updateOrder(tree);
                    }
                },
            });

            // Store reference to avoid re-init
            el._sortable = this.sortable;
        },

        destroy() {
            if (this.sortable) {
                this.sortable.destroy();
                this.sortable = null;
                this.$el._sortable = null;
            }
        },
    };
}

// Register with Alpine when it's ready
document.addEventListener('alpine:init', () => {
    Alpine.data('menuSortable', menuSortable);
});

// Handle "menu-saved" event — show a brief feedback flash
document.addEventListener('livewire:initialized', () => {
    Livewire.on('menu-saved', () => {
        const flash = document.getElementById('fmm-autosave-flash');
        if (flash) {
            flash.classList.add('fmm-flash-visible');
            setTimeout(() => flash.classList.remove('fmm-flash-visible'), 2000);
        }
    });
});
