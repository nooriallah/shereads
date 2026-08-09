import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Afacad', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // SHEREADS brand colors
                primary: {
                    DEFAULT: '#05653D',
                    50: '#E6F4EE',
                    100: '#C2E4D4',
                    200: '#8BCBAB',
                    300: '#54B183',
                    400: '#2A8B5D',
                    500: '#05653D',
                    600: '#045634',
                    700: '#03462A',
                    800: '#023721',
                    900: '#022818',
                },
                secondary: {
                    DEFAULT: '#E7B944',
                    50: '#FCF6E7',
                    100: '#F8EAC4',
                    200: '#F2D98D',
                    300: '#ECC968',
                    400: '#E7B944',
                    500: '#D9A426',
                    600: '#B5891F',
                    700: '#8A6818',
                    800: '#5F4810',
                    900: '#352808',
                },
            },
        },
    },

    plugins: [forms, typography],
};
