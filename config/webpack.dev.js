const webpack = require('webpack');
const merge = require('webpack-merge');
const common = require('./webpack.common.js');

const path = require('path');
const devServerHost = 'http://localhost';
const devServerPort = 9000;
const assetsFolderName = 'js';

module.exports = merge(common, {
	mode: 'development',
	devtool: 'eval',
	plugins: [
		new webpack.HotModuleReplacementPlugin(),
		new webpack.DefinePlugin({
			'process.env': {
				NODE_ENV: JSON.stringify('development'),
			},
		}),
	],
	output: {
		filename: 'bundle.js',
		path: path.resolve(__dirname, assetsFolderName),
		publicPath: devServerHost + ':' + devServerPort + '/'
	},
	devServer: {
		contentBase: path.join(__dirname, assetsFolderName),
		compress: true,
		port: devServerPort,
		overlay: {  // Вывод ошибок и предупреждений сборки в HTML
			warnings: true,
			errors: true
		},
		headers: {
			'Access-Control-Allow-Origin': '*'
		}
	}
});