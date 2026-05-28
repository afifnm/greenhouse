import './bootstrap';
import './sales';

const applyThemeLabel = () => {
    const label = document.querySelector('[data-theme-label]');
    if (!label) {
        return;
    }

    label.textContent = document.documentElement.classList.contains('dark') ? 'Light' : 'Dark';
};

document.addEventListener('DOMContentLoaded', () => {
    applyThemeLabel();

    document.getElementById('theme-toggle')?.addEventListener('click', () => {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        applyThemeLabel();
    });
});
