/** @type {import('tailwindcss').Config} */

const colors = require('tailwindcss/colors')

// We define colors here so that they can be reused
const mainColor = 'sky'
const grayColor = 'slate'
const greenColor = 'green'

const hasDarkHeader = true
const hasDarkFooter = true

const black = '#000000'
const foreground = colors[grayColor][900]
const backgroundDark = colors[grayColor][900]
const primary = colors[mainColor][900]
const lightDark = primary
const secondary = colors[mainColor][600]
const secondaryDark = colors[mainColor][400]
const light = colors[mainColor][200]
const primaryDark = colors[grayColor][200]
const white = '#ffffff'
const background = colors.neutral[100]
const gray = colors[grayColor]
const green = colors[greenColor]
const warning = colors['orange'][700]
const warningDark = colors['orange'][400]
const error = colors['red'][600]
const errorDark = colors['red'][400]

const transparent = 'transparent'


module.exports = {

    darkMode: 'class',

    content: [
        "./templates/**/*.{twig,svg}",
        './resources/js/**/*.js',
    ],

    theme: {

        // https://coolors.co/132a14-f1f9f1-2c5f2d-a8d8ac-6e8e43
        colors: {

            transparent: 'transparent',
            current: 'currentColor',

            // Page background
            background: {
                DEFAULT: background,
                'dark': backgroundDark
            },

            // Page foreground (aka default text)
            foreground: {
                DEFAULT: foreground,
                'dark': primaryDark
            },

            // Headings, button background
            primary: {
                DEFAULT: primary,
                dark: primaryDark
            },

            // No so heavy text, still good contrast to background. Colored background without text
            secondary: {
                DEFAULT: secondary,
                dark: secondaryDark
            },

           // Light background/text, good contrast to foreground/primary
            light: {
                DEFAULT: light,
                dark: lightDark
            },

            // Gray scale, black and white. Special usages only
            white: white,
            black: black,
            gray: gray,
            green: green,

            // Error messages

            error: {
                DEFAULT: error,
                dark: errorDark
            },

            warning: {
                DEFAULT: warning,
                dark: warningDark
            },

            // Header and footer
            'header-foreground': {
                DEFAULT: hasDarkHeader ? background : foreground,
                dark: background
            },

            'header-background': {
                DEFAULT: hasDarkHeader ? primary : background,
                dark: primary
            },

            'header-border': {
                DEFAULT: hasDarkHeader ? transparent : colors.gray[300],
                dark: transparent
            },

            'footer-foreground': {
                DEFAULT: hasDarkFooter ? background : foreground,
                dark: background
            },

            'footer-background': {
                DEFAULT: hasDarkFooter ? primary : background,
                dark: primary
            },

            'footer-border': {
                DEFAULT: hasDarkFooter ? transparent : colors.gray[300],
                dark: transparent
            },
        },

        // cannot add a smaller value into 'extend' because order matters
        screens: {
            xs: '480px',
            sm: '640px',
            md: '768px',
            lg: '1024px',
            xl: '1280px',
            '2xl': '1536px',
        },

        extend: {

            fontFamily: {
                sans: ['"Open Sans"', 'sans-serif'],
                headings: ['Raleway', 'sans-serif'],
                serif: ['"PT Serif"', 'serif']
            },

            aspectRatio: {
                '4/3': '4 / 3',
            },

            // Custom color scheme with custom colors
            typography: ({theme}) => ({
                custom: {
                    css: {
                        '--tw-prose-body': theme('colors.foreground.DEFAULT'),
                        '--tw-prose-headings': theme('colors.primary.DEFAULT'),
                        '--tw-prose-lead': theme('colors.primary.DEFAULT'),
                        '--tw-prose-links': theme('colors.foreground.DEFAULT'),
                        '--tw-prose-bold': theme('colors.primary.DEFAULT'),
                        '--tw-prose-counters': theme('colors.primary.DEFAULT'),
                        '--tw-prose-bullets': theme('colors.primary.DEFAULT'),
                        '--tw-prose-hr': theme('colors.primary.DEFAULT'),
                        '--tw-prose-quotes': theme('colors.primary.DEFAULT'),
                        '--tw-prose-quote-borders': theme('colors.primary.DEFAULT'),
                        '--tw-prose-captions': theme('colors.foreground.DEFAULT'),
                        '--tw-prose-code': theme('colors.white'),
                        '--tw-prose-pre-code': theme('colors.foreground-DEFAULT'),
                        '--tw-prose-pre-bg': theme('colors.background-dark'),
                        '--tw-prose-th-borders': theme('colors.gray[300]'),
                        '--tw-prose-td-borders': theme('colors.gray[200]'),
                        '--tw-prose-invert-body': theme('colors.foreground.dark'),
                        '--tw-prose-invert-headings': theme('colors.primary.dark'),
                        '--tw-prose-invert-lead': theme('colors.gray[300]'),
                        '--tw-prose-invert-links': theme('colors.foreground.dark'),
                        '--tw-prose-invert-bold': theme('colors.primary.dark'),
                        '--tw-prose-invert-counters': theme('colors.primary.dark'),
                        '--tw-prose-invert-bullets': theme('colors.primary.dark'),
                        '--tw-prose-invert-hr': theme('colors.primary.dark'),
                        '--tw-prose-invert-quotes': theme('colors.primary.dark'),
                        '--tw-prose-invert-quote-borders': theme('colors.primary.dark'),
                        '--tw-prose-invert-captions': theme('colors.foreground.dark'),
                        '--tw-prose-invert-code': theme('colors.white'),
                        '--tw-prose-invert-pre-code': theme('colors.white'),
                        '--tw-prose-invert-pre-bg': theme('colors.black'),
                        '--tw-prose-invert-th-borders': theme('colors.gray[600]'),
                        '--tw-prose-invert-td-borders': theme('colors.gray[700]'),
                    }
                },
            })
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/forms')
    ]
}
