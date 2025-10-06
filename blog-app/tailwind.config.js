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
                primary: {
                    50: '#F1EFDC',
                    100: '#E6D2AA',
                    200: '#D36B00',
                    300: '#42032C',
                    400: '#3A0326',
                    500: '#320220',
                    600: '#2A021A',
                    700: '#220114',
                    800: '#1A010E',
                    900: '#120008',
                },
                accent: {
                    50: '#F1EFDC',
                    100: '#E6D2AA',
                    200: '#D36B00',
                    300: '#B85A00',
                    400: '#9D4A00',
                    500: '#823A00',
                    600: '#672A00',
                    700: '#4C1A00',
                    800: '#310A00',
                    900: '#160500',
                },
                warm: {
                    50: '#F1EFDC',
                    100: '#E6D2AA',
                    200: '#DBCA8A',
                    300: '#D0C56A',
                    400: '#C5C04A',
                    500: '#BABB2A',
                    600: '#9F9F0A',
                    700: '#848400',
                    800: '#696900',
                    900: '#4E4E00',
                },
                dark: {
                    50: '#F1EFDC',
                    100: '#E6D2AA',
                    200: '#D36B00',
                    300: '#42032C',
                    400: '#3A0326',
                    500: '#320220',
                    600: '#2A021A',
                    700: '#220114',
                    800: '#1A010E',
                    900: '#120008',
                }
            },
        },
    },

    plugins: [forms],
};
