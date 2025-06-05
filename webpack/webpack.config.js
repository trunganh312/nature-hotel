const { merge } = require('webpack-merge');
const commonConfig = require('./webpack.dev.config.js');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const CompressionPlugin = require('compression-webpack-plugin');
// const { BundleAnalyzerPlugin } = require('webpack-bundle-analyzer'); // Thêm dòng này
const TerserPlugin = require('terser-webpack-plugin');


module.exports = merge(commonConfig, {
    mode: 'production',
    performance: {
        maxEntrypointSize: 512000, // Kích thước tối đa cho entrypoint
        maxAssetSize: 512000, // Kích thước tối đa cho asset
    },
    optimization: {
        minimize: true,
        minimizer: [
            new TerserPlugin({
                terserOptions: {
                    format: {
                        comments: false,
                    },
                    compress: {
                        drop_console: true, // Loại bỏ console.log
                    }
                },
                extractComments: false,
            }),
            new CssMinimizerPlugin(),
        ],
        splitChunks: {
            chunks: 'all',
            maxInitialRequests: 5,
            cacheGroups: {
                vendors: {
                    test: /[\\/]node_modules[\\/]/,
                    name: 'vendors',
                    chunks: 'all',
                },
            },
        },
    },
    plugins: [
        new CompressionPlugin({
            test: /\.(js|css|html|svg)$/,
            algorithm: 'gzip',
        }),
        // new BundleAnalyzerPlugin({
        //     analyzerMode: 'static',
        //     openAnalyzer: false,
        //     generateStatsFile: true,
        //     statsFilename: 'bundle-stats.json'
        // }),
    ]
});