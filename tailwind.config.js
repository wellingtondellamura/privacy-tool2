import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#f5f7ff',
                    100: '#ebf0ff',
                    200: '#d1dbfe',
                    300: '#b1bffe',
                    400: '#879cfd',
                    500: '#5c73fb', // Primary focus color
                    600: '#4051ef',
                    700: '#333ed6',
                    800: '#2c34ae',
                    900: '#272f8a',
                    950: '#151853',
                },
                surface: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a',
                    950: '#020617',
                }
            },
            boxShadow: {
                'tactile': '0 2px 8px -2px rgba(15, 23, 42, 0.05), 0 4px 12px -4px rgba(15, 23, 42, 0.05)',
                'tactile-hover': '0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04)',
                'tactile-active': '0 1px 2px 0 rgba(15, 23, 42, 0.05)',
            },
            transitionTimingFunction: {
                'smooth': 'cubic-bezier(0.4, 0, 0.2, 1)',
                'bounce': 'cubic-bezier(0.34, 1.56, 0.64, 1)',
            }
        },
    },

    plugins: [forms],
};
