class ThemeManager {
    constructor() {
        this.theme = localStorage.getItem('theme') || 'dark';
        this.isTransitioning = false;
    }

    init() {
        // Apply theme immediately on load
        this.applyTheme(this.theme, false);

        // Listen for storage changes
        window.addEventListener('storage', (e) => {
            if (e.key === 'theme') {
                this.applyTheme(e.newValue || 'dark');
            }
        });
    }

    applyTheme(theme, animate = true) {
        if (this.isTransitioning && animate) return;

        const html = document.documentElement;

        if (animate) {
            this.isTransitioning = true;
            html.classList.add('theme-transitioning');

            // Remove transitioning class after animation
            setTimeout(() => {
                html.classList.remove('theme-transitioning');
                this.isTransitioning = false;
            }, 200);
        }

        html.classList.remove('dark', 'light');
        html.classList.add(theme);

        this.theme = theme;
        localStorage.setItem('theme', theme);
        this.updateToggleIcon(theme);
    }

    toggle() {
        const newTheme = this.theme === 'dark' ? 'light' : 'dark';
        this.applyTheme(newTheme, true);
    }

    updateToggleIcon(theme) {
        const sunIcon = document.getElementById('sun-icon');
        const moonIcon = document.getElementById('moon-icon');

        if (sunIcon && moonIcon) {
            if (theme === 'light') {
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
            } else {
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
            }
        }
    }
}

window.themeManager = new ThemeManager();
window.toggleTheme = function() {
    window.themeManager.toggle();
};