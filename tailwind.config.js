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
                'notion-bg': '#191919',
                'notion-bg-dark': '#202020',
                'notion-hover': 'rgba(255, 255, 255, 0.05)',
                'notion-border': 'rgba(255, 255, 255, 0.1)',
                'notion-blue': '#2383E2',
                'notion-text-primary': 'rgba(255, 255, 255, 0.9)',
                'notion-text-secondary': 'rgba(255, 255, 255, 0.5)',
                'card': '#191919',
                'canvas': '#191919',
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
