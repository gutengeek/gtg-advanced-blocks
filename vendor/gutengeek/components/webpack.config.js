const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const path = require('path');
const postcssPresetEnv = require( 'postcss-preset-env' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const IgnoreEmitPlugin = require( 'ignore-emit-webpack-plugin' );

const production = process.env.NODE_ENV === '';

module.exports = {
    ...defaultConfig,
    entry: {
        ...defaultConfig.entry,
        index: path.resolve( process.cwd(), 'dev', 'index.js' ),
        core: path.resolve( process.cwd(), 'dev', 'scss/core.scss' ),
    },
    module: {
        ...defaultConfig.module,
        rules: [
            ...defaultConfig.module.rules,
            {
                test: /\.(sc|sa|c)ss$/,
                exclude: /node_modules/,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader,
                    },
                    {
                        loader: 'css-loader',
                        options: {
                            sourceMap: ! production,
                        },
                    },
                    {
                        loader: 'postcss-loader',
                        options: {
                            ident: 'postcss',
                            plugins: () => [
                                postcssPresetEnv( {
                                    stage: 3,
                                    features: {
                                        'custom-media-queries': {
                                            preserve: false,
                                        },
                                        'custom-properties': {
                                            preserve: true,
                                        },
                                        'nesting-rules': true,
                                    },
                                } ),
                            ],
                        },
                    },
                    {
                        loader: 'sass-loader',
                        options: {
                            sourceMap: ! production,
                            prependData: (loaderContext) => {
				                return '@import "dev/scss/variables.scss";';
			              	},
                        },
                    },
                ],
            },
            {
                test: /\.(woff|woff2|eot|ttf|otf|svg|png|jpg|gif)(\?v=\d+\.\d+\.\d+)?$/,
                use: {
                    loader: 'url-loader', // this need file-loader
                    options: {
                        limit: 50000

                    }
                }
            },
        ]
    },
    plugins: [
        ...defaultConfig.plugins,
        new MiniCssExtractPlugin( {
            filename: '[name].css',
        } ),
        new IgnoreEmitPlugin( [ 'core.js' ] ),
    ],
};
