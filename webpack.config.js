/* Webpack Configuration
===================================================================================================================== */

// Load Core Modules:

const path = require('path');
const webpack = require('webpack');

// Load Plugin Modules:

const CopyWebpackPlugin = require('copy-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const CleanWebpackPlugin = require('clean-webpack-plugin');

// Configure Paths:

const PATHS = {
  ADMIN: {
    SRC: path.resolve(__dirname, 'admin/client/src'),
    DIST: path.resolve(__dirname, 'admin/client/dist'),
    BUNDLES: path.resolve(__dirname, 'admin/client/src/bundles'),
    PUBLIC: '/resources/silverware/silverware/admin/client/dist/'
  },
  MODULE: {
    SRC: path.resolve(__dirname, 'client/src'),
    DIST: path.resolve(__dirname, 'client/dist'),
    BUNDLES: path.resolve(__dirname, 'client/src/bundles'),
    PUBLIC: '/resources/silverware/silverware/client/dist/',
  },
  MODULES: path.resolve(__dirname, 'node_modules')
};

// Configure Style Loader:

const style = (env, loaders) => {
  return (env === 'production') ? ExtractTextPlugin.extract({
    fallback: 'style-loader',
    use: loaders
  }) : [{ loader: 'style-loader' }].concat(loaders);
};

// Configure Rules:

const rules = (env) => {
  return [
    {
      test: /\.js$/,
      use: [
        {
          loader: 'babel-loader'
        }
      ],
      exclude: [ PATHS.MODULES ]
    },
    {
      test: /\.css$/,
      use: style(env, [
        {
          loader: 'css-loader'
        },
        {
          loader: 'postcss-loader'
        }
      ])
    },
    {
      test: /\.scss$/,
      use: style(env, [
        {
          loader: 'css-loader'
        },
        {
          loader: 'postcss-loader'
        },
        {
          loader: 'sass-loader'
        }
      ])
    },
    {
      test: /\.(gif|jpg|png)$/,
      use: [
        {
          loader: 'url-loader',
          options: {
            name: 'images/[name].[ext]',
            limit: 10000
          }
        }
      ]
    }
  ];
};

// Configure Devtool:

const devtool = (env) => {
  return (env === 'production') ? false : 'source-map';
};

// Configure Plugins:

const plugins = (env, src, dist) => {
  
  // Define Common Plugins:
  
  var common = [
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery'
    })
  ];
  
  // Define Admin-Only Plugins:
  
  if (src === PATHS.ADMIN.SRC) {
    common.push(
      new CopyWebpackPlugin([
        { from: path.resolve(src, 'images/icons'), to: 'images/icons' }
      ])
    );
  }
  
  // Answer Common + Environment-Specific Plugins:
  
  return common.concat((env === 'production') ? [
    new CleanWebpackPlugin([ dist ], {
      verbose: true
    }),
    new ExtractTextPlugin({
      filename: 'styles/[name].css',
      allChunks: true
    }),
    new webpack.optimize.UglifyJsPlugin({
      output: {
        beautify: false,
        comments: false,
        semicolons: false
      },
      compress: {
        unused: false,
        warnings: false
      }
    })
  ] : [
    
  ]);
  
};

// Define Configuration:

const config = (env) => {
  return [
    {
      entry: {
        'bundle': path.resolve(PATHS.ADMIN.BUNDLES, 'bundle.js'),
        'preview': path.resolve(PATHS.ADMIN.BUNDLES, 'preview.js')
      },
      output: {
        path: PATHS.ADMIN.DIST,
        filename: 'js/[name].js',
        publicPath: PATHS.ADMIN.PUBLIC
      },
      module: {
        rules: rules(env)
      },
      devtool: devtool(env),
      plugins: plugins(env, PATHS.ADMIN.SRC, PATHS.ADMIN.DIST),
      resolve: {
        alias: {
          'silverstripe-admin': path.resolve(process.env.PWD, '../../silverstripe/admin/client/src')
        },
        modules: [
          PATHS.ADMIN.SRC,
          PATHS.MODULES
        ]
      },
      externals: {
        jquery: 'jQuery'
      }
    },
    {
      entry: {
        'bundle': path.resolve(PATHS.MODULE.BUNDLES, 'bundle.js')
      },
      output: {
        path: PATHS.MODULE.DIST,
        filename: 'js/[name].js',
        publicPath: PATHS.MODULE.PUBLIC
      },
      module: {
        rules: rules(env)
      },
      devtool: devtool(env),
      plugins: plugins(env, PATHS.MODULE.SRC, PATHS.MODULE.DIST),
      resolve: {
        alias: {
          'bootstrap': path.resolve(process.env.PWD, '../../../themes/silverware-theme/node_modules/bootstrap'),
          'silverware-theme': path.resolve(process.env.PWD, '../../../themes/silverware-theme/source')
        },
        modules: [
          PATHS.MODULE.SRC,
          PATHS.MODULES
        ]
      },
      externals: {
        jquery: 'jQuery'
      }
    }
  ];
};

// Define Module Exports:

module.exports = (env = {development: true}) => {
  process.env.NODE_ENV = (env.production ? 'production' : 'development');
  console.log(`Running in ${process.env.NODE_ENV} mode...`);
  return config(process.env.NODE_ENV);
};
