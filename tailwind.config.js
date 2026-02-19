import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                coffee: {
                    50: '#faf8f5',
                    100: '#f5f0e8',
                    200: '#e8ddd0',
                    300: '#d4c4a8',
                    400: '#b89d7a',
                    500: '#9d7a5c',
                    600: '#7a5f47', // Más oscuro pero no tan oscuro
                    700: '#6b523f',
                    800: '#5a4535',
                    900: '#4a392e',
                },
                green: {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#22c55e',
                    600: '#16a34a',
                    700: '#15803d',
                    800: '#166534',
                    900: '#14532d',
                },
            },
        },
    },

    plugins: [forms],
};
