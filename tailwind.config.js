import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class', // Mengaktifkan mode gelap
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono], // Font monospace untuk SKU/Barcode
            },
            colors: {
                'wms-bg': '#0f172a',         // slate-900 (Background utama)
                'wms-card': '#1e293b',       // slate-800 (Container/Card)
                'wms-border': '#334155',     // slate-700 (Border pemisah)
                'wms-accent': '#22d3ee',     // cyan-400 (Aksen neon/aktif)
                'wms-accent-hover': '#06b6d4', // cyan-500 (Hover state)
                'wms-text': '#e2e8f0',       // slate-200 (Teks utama)
                'wms-muted': '#94a3b8',      // slate-400 (Teks sekunder/muted)
            },
            boxShadow: {
                'neon': '0 0 10px rgba(34, 211, 238, 0.3)',
                'neon-lg': '0 0 20px rgba(34, 211, 238, 0.4)',
            }
        },
    },
    plugins: [forms],
};