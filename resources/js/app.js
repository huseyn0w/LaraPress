import './bootstrap';

import Alpine from 'alpinejs';
import { registerFrontComponents, initDarkToggle, toggleDarkMode, trapFocus } from './front';

// Expose helpers on window so inline Alpine x-data closures can reference them.
window.toggleDarkMode = toggleDarkMode;
window.trapFocus = trapFocus;

// Register the public-theme Alpine components (like / comment / reveal / menu / mobileDrawer).
registerFrontComponents(Alpine);

window.Alpine = Alpine;

Alpine.start();

// Initialise the public dark-mode toggle button (if present).
document.addEventListener('DOMContentLoaded', () => {
    initDarkToggle(document.querySelector('[data-dark-toggle]'));
});
