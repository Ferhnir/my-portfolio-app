/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./index.html",
        "./src/**/*.{vue,js,ts,jsx,tsx}",
    ],
    theme: {
        extend: {
            colors: {
                'accent-bright': '#c6e000',
                'dark-charcoal': '#1a1a1a',
            }
        },
    },
    plugins: [],
}
