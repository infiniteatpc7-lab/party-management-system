import { initThemeSystem } from './theme.js';
import { initCustomSelects } from './custom-select.js';

function bootstrapApp() {
    initThemeSystem();
    initCustomSelects();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootstrapApp);
} else {
    bootstrapApp();
}
