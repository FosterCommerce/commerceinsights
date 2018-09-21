const path = require('path');
const {BundleAnalyzerPlugin} = require('webpack-bundle-analyzer');

const isProduction = process.env.NODE_ENV === 'production'

module.exports = {
    entry: './src/resources/index.js',
    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: isProduction ? 'bundle.min.js' : 'bundle.js'
    },
    mode: isProduction ? 'production' : 'development',
    plugins: [
      new BundleAnalyzerPlugin({
        analyzerMode: 'static',
        openAnalyzer: false,
        reportFilename: '../webpack-bundle-report.html'
      })
    ],
};