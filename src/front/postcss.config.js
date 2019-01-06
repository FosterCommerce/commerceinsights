const tailwindcss = require('tailwindcss')

module.exports = {
  // parser: 'postcss-safe-parser',
  plugins: [
    require('tailwindcss')('./tailwind.js'),
    require('autoprefixer'),
    require('postcss-preset-env'),
    require('postcss-import')
  ]
}