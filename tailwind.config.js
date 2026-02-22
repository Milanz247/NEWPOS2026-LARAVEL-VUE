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
                    dark:    '#0F172A',
                    darker:  '#020617',
                    light:   '#F9FAFB',
                    lighter: '#F3F4F6',
                    accent:  '#64748B',          // Subtle slate for hover states
                    muted:   '#94A3B8',          // Muted text, placeholders
                    border:  '#E2E8F0',          // Hairline borders
                    surface: '#FFFFFF',          // Card / Panel surface
                    ring:    '#CBD5E1',          // Focus rings
                },
            },

            borderRadius: {
                'xl':  '12px',
                '2xl': '16px',
                '3xl': '24px',
            },

            boxShadow: {
                // Diffused / "floating" material shadows
                'soft-xs':  '0 1px 2px 0 rgba(15,23,42,0.04)',
                'soft-sm':  '0 1px 3px 0 rgba(15,23,42,0.06), 0 1px 2px -1px rgba(15,23,42,0.04)',
                'soft':     '0 4px 6px -1px rgba(15,23,42,0.06), 0 2px 4px -2px rgba(15,23,42,0.04)',
                'soft-md':  '0 6px 16px -4px rgba(15,23,42,0.08), 0 2px 6px -2px rgba(15,23,42,0.04)',
                'soft-lg':  '0 12px 32px -6px rgba(15,23,42,0.10), 0 4px 10px -4px rgba(15,23,42,0.06)',
                'soft-xl':  '0 20px 48px -10px rgba(15,23,42,0.12), 0 8px 16px -6px rgba(15,23,42,0.06)',
                'glow-dark':'0 0 0 3px rgba(15,23,42,0.12)',
            },

            backgroundImage: {
                // Subtle gradient for primary action buttons
                'btn-primary': 'linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #334155 100%)',
                'btn-primary-hover': 'linear-gradient(135deg, #020617 0%, #0F172A 50%, #1E293B 100%)',
            },

            transitionTimingFunction: {
                'smooth': 'cubic-bezier(0.4, 0, 0.2, 1)',
            },

            backdropBlur: {
                'glass': '12px',
            },
        },
    },

    plugins: [forms],
};
