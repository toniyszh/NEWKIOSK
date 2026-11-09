/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./**/*.{html,php,js}"],
  theme: {
    extend: {
      fontFamily: {
        Montserrat: ['"Montserrat"', "sans-serif"],
      },
      colors: {
        primary: "var(--primary-color)",
        secondary: "var(--secondary-color)",
      },
    },
    plugins: [],
  },
};
