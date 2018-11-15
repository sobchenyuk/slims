const webpack = require('webpack');
const path = require('path');
const devServerHost = 'http://localhost';
const devServerPort = 8000;
const assetsFolderName = 'js';

const config = {
	mode: 'development',
	entry: './src/js/index.js',
	resolve: {
		extensions: [
			'.webpack.js',
			'.web.js',
			'.ts',
			'.tsx',
			'.js',
			'.jsx',
			'.css',
		],
		modules: ['node_modules'],
	},
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
};

module.exports = config;