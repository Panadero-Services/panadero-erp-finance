import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

 theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
              maxWidth: {
                '5xl': '80rem',
                '8xl': '96rem',
                '9xl': '120rem',
              },
            fontSize: {
              xxxs: ['8px', '0.8rem'],
              xxs: ['10px', '1.0rem'],
              xs: ['12px', '1.4rem'],
              sm: ['14px', '1.5rem'],
              base: ['16px', '1.75rem'],
              lg: ['18px', '2rem'],
              xl: ['20px', '2.1rem'],
              xxl: ['22px', '2.2rem'],
              xxxl: ['24px', '2.3rem'],
              header: ['36px', '3.5rem'],
            },

            colors: {
                'indigo-dark-50' : '#4214cd',
                'indigo-dark-100' : '#3b12b8',
                'indigo-dark-200' : '#3410a4',
                'indigo-dark-300' : '#2e0e8f',
                'indigo-dark-400' : '#270c7b',
                'indigo-dark-500' : '#210a66',
                'indigo-dark-600' : '#1a0852',
                'indigo-dark-700' : '#13063d',
             //   'indigo-dark-800' : '#030c29',
                'indigo-dark-800' : '#0f0428',
                'indigo-dark-900' : '#060214',
                'indigo-light-900' : '#2641cb',
                'indigo-light-800' : '#3e56d1',
                'indigo-light-700' : '#566bd7',
                'indigo-light-600' : '#6e80dc',
                'indigo-light-500' : '#8695e2',
                'indigo-light-400' : '#9eaae8',
                'indigo-light-300' : '#b6bfed',
                'indigo-light-200' : '#ced4f3',
                'indigo-light-100' : '#e6e9f9',
                'indigo-light-50' : '#edeffe',
                'indigo-light-25' : '#f6f7fe',
            },

        },

    },

    plugins: [forms, typography],
};
