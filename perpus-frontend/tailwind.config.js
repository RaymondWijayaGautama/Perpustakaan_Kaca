/** @type {import('tailwindcss').Config} */
export default {
  // 1. Ini untuk memperbaiki error "content is missing" kamu
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      // 2. Memasukkan Color System Boda
      colors: {
        'brand-primary': '#265F9C', 
        'brand-secondary': '#EDA60F',
        'brand-accent': '#EDD15B',
        'background-default': '#F6F7F9',
        'success-boda': '#2E7D32',
        'error-boda': '#C62828',
        'neutral-900': '#1A1A1A', 
      },
      // 3. Memasukkan Typography Boda
      fontFamily: {
        'montserrat': ['Montserrat', 'sans-serif'], 
        'roboto': ['Roboto', 'sans-serif'], 
      },
      // 4. Memasukkan Border Radius Boda
      borderRadius: {
        'boda-sm': '4px', 
        'boda-md': '8px', 
        'boda-lg': '16px',
      }
    },
  },
  plugins: [],
}