const path                  = require('path');
const ExtractTextPlugin     = require('extract-text-webpack-plugin');
const WebpackAssetsManifest = require('webpack-assets-manifest');
const CleanWebpackPlugin    = require('clean-webpack-plugin');

const env          = process.env.NODE_ENV;
const isProduction = env === 'production';

/**
 * @typedef LibrariesHash
 * @property {object} [entryChunkName: string] A unique entry ID
 * @property {array} entryChunkName.entry - A list of entries to bundle with the library
 * @property {string} [entryChunkName.fileName] - The target file name (if omitted, the entry entryChunkName will be used
 * @property {string} [entryChunkName.var] - The name of the global variable to which the library will be assigned
 *
 * @type LibrariesHash
 */
const libraries = {
	'otgs-switcher':            {
		entry: ['./src/js/otgsSwitcher.js'],
		var:   'otgsSwitcher',
	},
	'otgs-popover-tooltip':     {
		entry: ['./src/js/otgsPopoverTooltip.js'],
		var:   'otgsPopoverTooltip',
	},
	'otgs-table-sticky-header': {
		entry: ['./src/js/otgsTableStickyHeader.js'],
		var:   'otgsTableStickyHeader',
	},
};

const getEntries       = () => {
	const entries = {};
	Object.keys(libraries).map(key => entries[key] = libraries[key].entry);

	return entries;
};
const getVars          = () => Object.keys(libraries).map(key => {
	if (libraries.hasOwnProperty(key) && libraries[key].hasOwnProperty('var') && libraries[key].var) {
		return libraries[key].var;
	}
	return key;
});
const getEntryFileName = (chunk) => {
	if (libraries.hasOwnProperty(chunk.id) && libraries[chunk.id].hasOwnProperty('fileName') && libraries[chunk.id].fileName) {
		return libraries[chunk.id].fileName;
	}
	return path.join(chunk.name + '.js');
};

module.exports = {
	entry:       getEntries,
	output:      {
		path:              path.join(__dirname, 'dist'),
		filename:          chunkData => path.join('js', getEntryFileName(chunkData.chunk) + '?ver=' + chunkData.chunk.hash),
			chunkFilename: '[id].[name].js?ver=[chunkhash]',
		library:           getVars(),
		libraryTarget:     'var'
	},
	module:      {
		rules: [
			{
				loader:  'babel-loader',
				test:    /\.js$/,
				exclude: /node_modules/,
				query:   {
					presets: ['es2015'],
				},
			},
			{
				test: /\.s?css$/,
				use:  ExtractTextPlugin.extract({
					fallback: 'style-loader',
					use:      [
						{
							loader:  'css-loader',
							options: {
								sourceMap: !isProduction,
								minimize:  isProduction,
							},
						},
						{
							loader:  'sass-loader',
							options: {
								sourceMap: !isProduction,
							},
						},
						{
							loader: 'postcss-loader',
						},
					],
				}),
			},
		],
	},
		plugins: [
			new CleanWebpackPlugin(['dist']),
			new ExtractTextPlugin({
				filename: path.join('css', '[name].css?ver=[chunkhash]'),
			}),
			new WebpackAssetsManifest({
				output:      path.join(__dirname, 'dist', 'assets.json'),
				entrypoints: true,
			}),
		],
		devtool: isProduction ? '' : 'inline-source-map'
	};
