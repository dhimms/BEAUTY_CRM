import defaultTheme from 'tailwindcss/defaultTheme';

export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                cream: {
                    DEFAULT: '#FAF7F2',
                    dark: '#F0EBE3',
                },
                charcoal: {
                    50: '#F8F8F8',
                    100: '#F0F0F0',
                    200: '#E4E4E4',
                    300: '#D1D1D1',
                    400: '#B4B4B4',
                    500: '#8A8A8A',
                    600: '#6D6D6D',
                    700: '#4A4A4A',
                    800: '#2D2D2D',
                    900: '#1A1A1A',
                    950: '#0D0D0D',
                },
            },
            fontFamily: {
                serif: ['Cormorant Garamond', 'Georgia', ...defaultTheme.fontFamily.serif],
                sans: ['DM Sans', ...defaultTheme.fontFamily.sans],
                mono: ['DM Mono', ...defaultTheme.fontFamily.mono],
            },
            borderWidth: {
                3: '3px',
            },
        },
    },
    plugins: [],
};