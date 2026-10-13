/**
 * PMS Theme Manager - Supporting Classic, Black Iris & Frozen, Shadow & Citrus
 */

export function initThemeSystem() {
    const savedTheme = localStorage.getItem('pms_theme') || 'default';
    applyTheme(savedTheme, false);

    // Close theme dropdown on outside click
    document.addEventListener('click', (e) => {
        const container = document.getElementById('theme-dropdown-container');
        const dropdown = document.getElementById('theme-menu-dropdown');
        if (container && dropdown && !container.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Update UI when DOM is ready
    updateThemeUI(savedTheme);
}

export function setAppTheme(theme) {
    if (!['default', 'black-iris', 'shadow-citrus'].includes(theme)) {
        theme = 'default';
    }
    localStorage.setItem('pms_theme', theme);
    applyTheme(theme, true);
}

export function applyTheme(theme, dispatchEvent = true) {
    document.documentElement.setAttribute('data-theme', theme);
    updateThemeUI(theme);

    // Close dropdown menu if open
    const dropdown = document.getElementById('theme-menu-dropdown');
    if (dropdown) {
        dropdown.classList.add('hidden');
    }

    if (dispatchEvent) {
        window.dispatchEvent(new CustomEvent('pms:theme-change', { detail: { theme } }));
    }
}

export function toggleThemeMenu(event) {
    if (event) {
        event.stopPropagation();
    }
    const dropdown = document.getElementById('theme-menu-dropdown');
    if (dropdown) {
        dropdown.classList.toggle('hidden');
    }
}

export function updateThemeUI(theme) {
    // 1. Update dropdown active checks
    const options = document.querySelectorAll('.theme-option-item');
    options.forEach(opt => {
        const optTheme = opt.getAttribute('data-theme-option');
        const check = opt.querySelector('.theme-check');
        if (optTheme === theme) {
            opt.classList.add('bg-slate-50', 'theme-active-item');
            if (check) check.classList.remove('hidden');
        } else {
            opt.classList.remove('bg-slate-50', 'theme-active-item');
            if (check) check.classList.add('hidden');
        }
    });

    // 2. Update mobile buttons
    const mobileBtns = document.querySelectorAll('.theme-choice-btn');
    mobileBtns.forEach(btn => {
        const btnTheme = btn.getAttribute('data-theme-btn');
        if (btnTheme === theme) {
            btn.classList.add('ring-2', 'ring-indigo-500', 'border-transparent', 'theme-mobile-active');
        } else {
            btn.classList.remove('ring-2', 'ring-indigo-500', 'border-transparent', 'theme-mobile-active');
        }
    });

    // 3. Update main header button swatch and label
    const currentSwatch = document.getElementById('theme-current-swatch');
    const currentLabel = document.getElementById('theme-current-label');

    if (currentSwatch && currentLabel) {
        if (theme === 'black-iris') {
            currentSwatch.innerHTML = '<span class="w-full h-full bg-[#080813] flex items-center justify-center"><span class="w-2 h-2 rounded-full bg-[#A0BDDB]"></span></span>';
            currentLabel.textContent = 'Black Iris';
        } else if (theme === 'shadow-citrus') {
            currentSwatch.innerHTML = '<span class="w-full h-full bg-[#141414] flex items-center justify-center"><span class="w-2 h-2 rounded-full bg-[#E4FD97]"></span></span>';
            currentLabel.textContent = 'Shadow Citrus';
        } else {
            currentSwatch.innerHTML = '<span class="w-full h-full bg-indigo-600"></span>';
            currentLabel.textContent = 'Classic';
        }
    }
}

// Expose globally for inline onclick handlers in Blade views
if (typeof window !== 'undefined') {
    window.setAppTheme = setAppTheme;
    window.toggleThemeMenu = toggleThemeMenu;
    window.updateThemeUI = updateThemeUI;
}
