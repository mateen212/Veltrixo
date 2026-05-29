import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans:     ['Inter', ...defaultTheme.fontFamily.sans],
                display:  ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
                mono:     ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },

            colors: {
                brand: {
                    50:  '#EEF2FF',
                    100: '#E0E7FF',
                    200: '#C7D2FE',
                    300: '#A5B4FC',
                    400: '#818CF8',
                    500: '#6366F1',
                    600: '#4F46E5',
                    700: '#4338CA',
                    800: '#3730A3',
                    900: '#312E81',
                    950: '#1E1B4B',
                },
                surface: {
                    DEFAULT: '#FFFFFF',
                    muted:   '#F8F9FC',
                    subtle:  '#F1F3F9',
                },
                ink: {
                    DEFAULT: '#111827',
                    secondary: '#374151',
                    muted:     '#6B7280',
                    faint:     '#9CA3AF',
                    inverted:  '#FFFFFF',
                },
                border: {
                    DEFAULT: '#E5E7EB',
                    muted:   '#F3F4F6',
                    strong:  '#D1D5DB',
                },
                canvas: {
                    DEFAULT: '#0C0C1D',
                    muted:   '#111127',
                    subtle:  '#16162A',
                    border:  'rgba(255,255,255,0.06)',
                },
            },

            boxShadow: {
                'xs':    '0 1px 2px 0 rgba(0,0,0,0.04)',
                'sm':    '0 1px 3px 0 rgba(0,0,0,0.06), 0 1px 2px -1px rgba(0,0,0,0.04)',
                'card':  '0 2px 8px -2px rgba(0,0,0,0.06), 0 0 0 1px rgba(0,0,0,0.04)',
                'md':    '0 4px 16px -4px rgba(0,0,0,0.08), 0 2px 6px -2px rgba(0,0,0,0.06)',
                'lg':    '0 8px 32px -8px rgba(0,0,0,0.12), 0 4px 12px -4px rgba(0,0,0,0.08)',
                'xl':    '0 16px 48px -16px rgba(0,0,0,0.16), 0 8px 24px -8px rgba(0,0,0,0.10)',
                'glow':  '0 0 0 3px rgba(99,102,241,0.15)',
                'glow-lg': '0 0 40px -10px rgba(99,102,241,0.35)',
                'inner-xs': 'inset 0 1px 2px 0 rgba(0,0,0,0.04)',
            },

            borderRadius: {
                'xs':  '4px',
                'sm':  '6px',
                'DEFAULT': '8px',
                'md':  '10px',
                'lg':  '12px',
                'xl':  '16px',
                '2xl': '20px',
                '3xl': '24px',
            },

            transitionDuration: {
                '50':  '50ms',
                '150': '150ms',
                '250': '250ms',
                '350': '350ms',
                '450': '450ms',
            },

            transitionTimingFunction: {
                'spring':    'cubic-bezier(0.34, 1.56, 0.64, 1)',
                'smooth':    'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
                'in-quart':  'cubic-bezier(0.5, 0, 1, 0)',
                'out-quart': 'cubic-bezier(0, 0, 0.5, 1)',
            },

            animation: {
                'fade-in':      'fadeIn 0.3s cubic-bezier(0.25,0.46,0.45,0.94) both',
                'fade-up':      'fadeUp 0.4s cubic-bezier(0.25,0.46,0.45,0.94) both',
                'fade-down':    'fadeDown 0.3s cubic-bezier(0.25,0.46,0.45,0.94) both',
                'scale-in':     'scaleIn 0.25s cubic-bezier(0.34,1.56,0.64,1) both',
                'slide-left':   'slideLeft 0.3s cubic-bezier(0.25,0.46,0.45,0.94) both',
                'slide-right':  'slideRight 0.3s cubic-bezier(0.25,0.46,0.45,0.94) both',
                'pulse-subtle': 'pulseSubtle 2s ease-in-out infinite',
                'shimmer':      'shimmer 1.5s infinite linear',
                'spin-slow':    'spin 3s linear infinite',
                'bounce-sm':    'bounceSm 1s ease-in-out infinite',
                'count-up':     'countUp 0.6s cubic-bezier(0.25,0.46,0.45,0.94) both',
            },

            keyframes: {
                fadeIn:      { from: { opacity: '0' },                         to: { opacity: '1' } },
                fadeUp:      { from: { opacity: '0', transform: 'translateY(16px)' }, to: { opacity: '1', transform: 'translateY(0)' } },
                fadeDown:    { from: { opacity: '0', transform: 'translateY(-16px)' }, to: { opacity: '1', transform: 'translateY(0)' } },
                scaleIn:     { from: { opacity: '0', transform: 'scale(0.92)' }, to: { opacity: '1', transform: 'scale(1)' } },
                slideLeft:   { from: { opacity: '0', transform: 'translateX(24px)' }, to: { opacity: '1', transform: 'translateX(0)' } },
                slideRight:  { from: { opacity: '0', transform: 'translateX(-24px)' }, to: { opacity: '1', transform: 'translateX(0)' } },
                pulseSubtle: { '0%,100%': { opacity: '1' }, '50%': { opacity: '0.6' } },
                shimmer:     { from: { backgroundPosition: '-200% 0' }, to: { backgroundPosition: '200% 0' } },
                bounceSm:    { '0%,100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-4px)' } },
                countUp:     { from: { opacity: '0', transform: 'translateY(8px)' }, to: { opacity: '1', transform: 'translateY(0)' } },
            },

            spacing: {
                '4.5': '1.125rem',
                '13':  '3.25rem',
                '15':  '3.75rem',
                '18':  '4.5rem',
                '22':  '5.5rem',
            },

            backgroundImage: {
                'brand-gradient':   'linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%)',
                'brand-gradient-r': 'linear-gradient(225deg, #4F46E5 0%, #7C3AED 100%)',
                'shimmer-gradient': 'linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.6) 50%, transparent 100%)',
                'glass':            'linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%)',
                'noise':            "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='300'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='300' height='300' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E\")",
            },

            backdropBlur: {
                'xs': '2px',
            },
        },
    },

    plugins: [forms],
};
