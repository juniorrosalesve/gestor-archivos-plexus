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
        },
    },
    daisyui: {
        themes: [
            {
                plexus_theme: {
                    "primary": "#e7e5e4",  
                    "secondary": "#9ca3af",  
                    "accent": "#d59b6c",
                    "neutral": "#836b5d",
                    "base-100": "#f2f2f2",    
                    "info": "#42aebd",     
                    "success": "#489380",
                    "warning": "#eb8014", 
                    "error": "#e01a2e",
                },
            },
        ],
    },
    plugins: [forms, require("daisyui")],
};
