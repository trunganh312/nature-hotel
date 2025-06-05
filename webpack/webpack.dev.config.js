const path = require("path");
const { VueLoaderPlugin } = require("vue-loader");
const { WebpackManifestPlugin } = require("webpack-manifest-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
    mode: "development",
    entry: "./index.js",
    output: {
        filename: "[name].[contenthash].js",
        path: path.resolve(__dirname, "admin/public/theme/bundle"),
        clean: true
    },
    resolve: {
        extensions: [".js", ".vue", ".json"],
        alias: {
            vue$: path.resolve(__dirname, "node_modules/vue/dist/vue.esm-browser.prod.js"),
            "@lib": path.resolve(__dirname, "node_modules"),
            "@root": path.resolve(__dirname),
            "@app": path.resolve(__dirname, "../app"),
            "@admin": path.resolve(__dirname, "../admin"),
            "@components": path.resolve(__dirname, "../app/components")
        }
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: "vue-loader"
            },
            {
                test: /\.(css|scss)$/,
                use: [MiniCssExtractPlugin.loader, "css-loader", "postcss-loader", "sass-loader"]
            }
        ]
    },
    plugins: [
        new VueLoaderPlugin(),
        new WebpackManifestPlugin({
            fileName: "manifest.json",
            publicPath: ""
        }),
        new MiniCssExtractPlugin({
            filename: "[name].[contenthash].css",
            chunkFilename: "[id].[contenthash].css"
        })
    ]
};
