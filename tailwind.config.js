import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            // IBM Plex Sans es la fuente que carga el layout
            fontFamily: {
                sans: ['"IBM Plex Sans"', ...defaultTheme.fontFamily.sans],
                mono: ['"IBM Plex Mono"', ...defaultTheme.fontFamily.mono],
            },
            // Escala tipográfica del sistema (ver public/css/das-ui.css)
            fontSize: {
                meta: ['11px', { lineHeight: '16px' }],
                label: ['12.5px', { lineHeight: '18px' }],
                body: ['13.5px', { lineHeight: '20px' }],
                section: ['16px', { lineHeight: '24px' }],
                page: ['22px', { lineHeight: '28px' }],
            },
            colors: {
                brand: { DEFAULT: '#0F766E', hover: '#115E59', soft: '#F0FDFA' },
                estado: {
                    disponible: '#047857', cuarentena: '#B45309', vencido: '#BE123C',
                    pendiente: '#0369A1', controlado: '#6D28D9',
                },
            },
            borderRadius: { control: '8px', card: '12px' },
        },
    },

    plugins: [forms],
};
