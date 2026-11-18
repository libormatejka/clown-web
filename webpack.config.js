const path = require('path');
const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const WebpackAssetsManifest = require('webpack-assets-manifest');

module.exports = () => ({
	entry: {
		admin: path.resolve(__dirname, 'assets/admin.js'),
		front: path.resolve(__dirname, 'assets/front.js'),
		style: path.resolve(__dirname, 'assets/front/css/style.css'),
	},
	output: {
		filename: 'js/[name].js',
		path: path.resolve(__dirname, 'www/assets'),
		publicPath: '/assets/',
		clean: true,
	},
	optimization: {
		runtimeChunk: { name: 'runtime' },
	},
	module: {
		rules: [
			{
				test: /\.(png|jpg|gif|svg|ico|webp)$/,
				loader: 'file-loader',
				options: {
					name: 'img/[name].[ext]',
					esModule: false,
				},
				type: 'javascript/auto',
			},
			{
				// TinyMCE načítá tyto dva CSS soubory jako čistý text (`.toString()`) pro `content_style`,
				// ne jako běžný CSS modul — musí mít vlastní pravidlo před obecným `.css` pravidlem.
				test: /tinymce[/\\]skins[/\\].*content\.css$/,
				type: 'asset/source',
			},
			{
				test: /\.css$/,
				exclude: /tinymce[/\\]skins[/\\].*content\.css$/,
				use: [MiniCssExtractPlugin.loader, 'css-loader', 'postcss-loader'],
			},
		],
	},
	plugins: [
		new webpack.ProvidePlugin({
			$: 'jquery',
			jQuery: 'jquery',
			'window.jQuery': 'jquery',
		}),
		new MiniCssExtractPlugin({ filename: 'css/[name].css' }),
		new WebpackAssetsManifest({
			output: path.resolve(__dirname, 'config/manifest-assets.json'),
			publicPath: true,
		}),
	],
});
