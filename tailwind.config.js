import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/View/Middleware/ShareErrorsFromSession.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                'canvas': 'var(--color-canvas)',
                'card': 'var(--color-card)',
                'notion-blue': 'var(--color-notion-blue)',
                'notion-text-primary': 'var(--color-notion-text-primary)',
                'notion-text-secondary': 'var(--color-notion-text-secondary)',
                'notion-text-mono': 'var(--color-notion-text-mono)',
                'notion-border': 'var(--color-notion-border)',
                'notion-hover': 'var(--color-notion-hover)',
            },
            borderRadius: {
                'notion': '4px',
            },
            boxShadow: {
                'notion': '0 1px 2px rgba(0, 0, 0, 0.1)',
            },
        },
    },
    plugins: [],
};
