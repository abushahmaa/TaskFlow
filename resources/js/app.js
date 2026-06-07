import './bootstrap';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// ─── Dark mode initialization ─────────────────────────────────
// Apply dark class on <html> based on localStorage preference
(function () {
    const saved = localStorage.getItem('theme');
    if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    }
})();

// Toggle function available globally
window.toggleDarkMode = function () {
    const html = document.documentElement;
    if (html.classList.contains('dark')) {
        html.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    } else {
        html.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    }
};

// ─── Auto-dismiss alerts ──────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-auto-dismiss]').forEach(el => {
        const ms = parseInt(el.dataset.autoDismiss, 10) || 4000;
        setTimeout(() => el.remove(), ms);
    });
});
