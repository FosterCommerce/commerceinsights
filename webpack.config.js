const path = require('path');

module.exports = {
    entry: './src/resources/index.js',
    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: 'bundle.min.js'
    },
    mode: 'development'
};