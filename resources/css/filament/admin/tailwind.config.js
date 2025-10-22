/** @type {import('tailwindcss').Config} */
export default {
    content: [
      './resources/views/**/*.blade.php',
      './resources/css/filament/admin/**/*.css',
      './vendor/filament/**/*.blade.php',
      './vendor/filament/**/*.php',
    ],
    theme: {
      extend: {
        colors: {
          primary: {
            50: '#eff6ff',
            100: '#dbeafe',
            200: '#bfdbfe',
            300: '#93c5fd',
            400: '#60a5fa',
            500: '#3b82f6',
            600: '#2563eb',
            700: '#1d4ed8',
            800: '#1e40af',
            900: '#1e3a8a',
            950: '#172554',
          },
        },
        borderRadius: {
          xl: '1rem',
        },
        fontFamily: {
          sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        },
      },
    },
    plugins: [
      require('@tailwindcss/forms'),
      require('@tailwindcss/typography'),
    ],
  }
