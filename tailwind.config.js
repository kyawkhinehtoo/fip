const defaultTheme = require("tailwindcss/defaultTheme");
const path = require("path");
const colors = require("tailwindcss/colors");
module.exports = {
  purge: ["./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php", "./vendor/laravel/jetstream/**/*.blade.php", "./storage/framework/views/*.php", "./resources/views/**/*.blade.php", "./resources/js/**/*.vue", path.resolve(__dirname, "./node_modules/litepie-datepicker/**/*.js")],

  theme: {
    extend: {
      fontFamily: {
        sans: ["Nunito", ...defaultTheme.fontFamily.sans],
      },
      colors: {
        // Change with you want it
        "litepie-primary": colors.indigo, // color system for light mode
        "litepie-secondary": colors.coolGray, // color system for dark mode
        "lm-orange": colors.indigo,
        // 'lm-orange' : '#f27036',
        "lm-gray": "#656565",
      },
    },
  },

  variants: {
    extend: {
      opacity: ["disabled"],
      backgroundColor: ["active"],
      backgroundColor: ["hover"],
    },
    scrollbar: ["rounded"],
  },

  plugins: [require("@tailwindcss/forms"), require("@tailwindcss/typography"), require("tailwind-scrollbar")],
};
