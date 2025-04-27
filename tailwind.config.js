const defaultTheme = require('tailwindcss/defaultTheme');
const forms = require('@tailwindcss/forms');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    safelist: [
        'border',
        'border-gray-300',
        'px-4',
        'py-2',
        'bg-gray-100',
        'w-8',
        'h-8',
        'text-gray-200',
        'animate-spin',
        'dark:text-gray-600',
        'fill-blue-600',
        'fill-current',
        'sr-only'
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
