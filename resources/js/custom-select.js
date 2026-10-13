/**
 * Theme-Wise Custom Select Dropdown Enhancer
 * Replaces native OS select popups with fully themed custom dropdowns
 * supporting Shadow & Citrus, Black Iris, and Classic themes.
 */

export function initCustomSelects() {
    const selects = document.querySelectorAll('select.theme-select');
    selects.forEach(select => {
        if (select.dataset.customized === 'true') {
            return;
        }
        select.dataset.customized = 'true';

        // Hide original select visually but keep it in form flow for FormData/submit
        select.classList.add('sr-only');
        select.setAttribute('aria-hidden', 'true');
        select.tabIndex = -1;

        const isFullWidth = select.classList.contains('w-full');
        const isResponsiveAuto = select.classList.contains('sm:w-auto');

        // Container wrapper
        const wrapper = document.createElement('div');
        if (isFullWidth && isResponsiveAuto) {
            wrapper.className = 'custom-select-container relative w-full sm:w-auto shrink-0';
        } else if (isFullWidth) {
            wrapper.className = 'custom-select-container relative w-full';
        } else {
            wrapper.className = 'custom-select-container relative shrink-0';
        }

        // Trigger button
        const trigger = document.createElement('button');
        trigger.type = 'button';
        const triggerWidthClass = (isFullWidth && isResponsiveAuto) ? 'w-full sm:w-auto' : (isFullWidth ? 'w-full' : 'w-auto');
        trigger.className = `custom-select-trigger ${triggerWidthClass} flex items-center justify-between px-3 py-1.5 sm:py-2 rounded-xl text-xs sm:text-sm font-medium transition cursor-pointer text-left gap-2 whitespace-nowrap`;
        
        const label = document.createElement('span');
        label.className = 'custom-select-label truncate';
        const selectedOpt = select.options[select.selectedIndex] || select.options[0];
        label.textContent = selectedOpt ? selectedOpt.textContent : '';

        const icon = document.createElement('div');
        icon.className = 'custom-select-chevron shrink-0 ml-1.5 transition-transform duration-200 pointer-events-none';
        icon.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>`;

        trigger.appendChild(label);
        trigger.appendChild(icon);

        // Dropdown menu
        const menu = document.createElement('div');
        menu.className = 'custom-select-menu hidden absolute top-full left-0 min-w-full w-max mt-1.5 py-1 rounded-xl shadow-xl z-50 max-h-56 overflow-y-auto';

        // Populate options
        Array.from(select.options).forEach((option, idx) => {
            const item = document.createElement('div');
            item.className = 'custom-select-option px-3.5 py-2 text-xs sm:text-sm cursor-pointer transition select-none flex items-center justify-between whitespace-nowrap';
            item.dataset.value = option.value;
            item.textContent = option.textContent;

            if (idx === select.selectedIndex) {
                item.classList.add('selected');
            }

            item.addEventListener('click', (e) => {
                e.stopPropagation();
                select.value = option.value;
                label.textContent = option.textContent;
                
                // Update selected status
                menu.querySelectorAll('.custom-select-option').forEach(el => el.classList.remove('selected'));
                item.classList.add('selected');

                // Close menu
                menu.classList.add('hidden');
                icon.classList.remove('rotate-180');
                trigger.classList.remove('open');
                wrapper.style.zIndex = '';

                // Dispatch native change event so live search & forms catch it
                select.dispatchEvent(new Event('change', { bubbles: true }));
            });

            menu.appendChild(item);
        });

        // Toggle menu on trigger click
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = !menu.classList.contains('hidden');
            
            // Close other open custom dropdowns
            document.querySelectorAll('.custom-select-menu').forEach(m => {
                if (m !== menu) {
                    m.classList.add('hidden');
                    m.previousElementSibling?.querySelector('.custom-select-chevron')?.classList.remove('rotate-180');
                    m.previousElementSibling?.classList.remove('open');
                    const c = m.closest('.custom-select-container');
                    if (c) c.style.zIndex = '';
                }
            });

            if (isOpen) {
                menu.classList.add('hidden');
                icon.classList.remove('rotate-180');
                trigger.classList.remove('open');
                wrapper.style.zIndex = '';
            } else {
                menu.classList.remove('hidden');
                icon.classList.add('rotate-180');
                trigger.classList.add('open');
                wrapper.style.zIndex = '1300';

                // Check bounds to prevent horizontal clipping on screen edge
                const rect = trigger.getBoundingClientRect();
                if (rect.right + 40 > window.innerWidth || rect.left + 160 > window.innerWidth) {
                    menu.style.left = 'auto';
                    menu.style.right = '0';
                } else {
                    menu.style.left = '0';
                    menu.style.right = 'auto';
                }
            }
        });

        // Insert into DOM
        select.parentNode.insertBefore(wrapper, select);
        wrapper.appendChild(select);
        wrapper.appendChild(trigger);
        wrapper.appendChild(menu);
    });
}

// Global outside click & Escape handlers
if (typeof document !== 'undefined') {
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.custom-select-container')) {
            document.querySelectorAll('.custom-select-menu').forEach(menu => {
                menu.classList.add('hidden');
                menu.previousElementSibling?.querySelector('.custom-select-chevron')?.classList.remove('rotate-180');
                menu.previousElementSibling?.classList.remove('open');
                const c = menu.closest('.custom-select-container');
                if (c) c.style.zIndex = '';
            });
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.custom-select-menu').forEach(menu => {
                menu.classList.add('hidden');
                menu.previousElementSibling?.querySelector('.custom-select-chevron')?.classList.remove('rotate-180');
                menu.previousElementSibling?.classList.remove('open');
                const c = menu.closest('.custom-select-container');
                if (c) c.style.zIndex = '';
            });
        }
    });
}

if (typeof window !== 'undefined') {
    window.initCustomSelects = initCustomSelects;
}
