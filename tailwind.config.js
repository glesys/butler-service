/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.js",
    ],
    plugins: [
        require('tailwind-scrollbar')({ nocompatible: true }),
    ],
    theme: {
        fontFamily: {
            'sans': 'DM Sans, Helvetica, Arial, sans-serif',
            'mono': 'DM Mono, Lucida Console, Courier, monospace',
        },
        extend: {
            colors: {
                blue: {
                    dark: '#619EC6',
                    light: '#8AB7D5'
                },
                red: {
                    dark: '#BE5D5D',
                    light: '#CB8080'
                },
                yellow: {
                    dark: '#BDAA67'
                },
                green: {
                    dark: '#41A95C',
                    light: '#85D098'
                },
                gray: {
                    900: '#0E1111',
                    800: '#181D20',
                    700: '#21282C',
                    600: '#21282C',
                    500: '#363D45',
                    400: '#9FA5AD',
                    300: '#BABFC4',
                    200: '#D6D8DC',
                    100: '#F1F2F3',
                },
            },
        },
    },
};
