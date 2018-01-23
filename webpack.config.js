/* Webpack Configuration
===================================================================================================================== */

// Load Core:

const path    = require('path');
const webpack = require('webpack');

// Load Plugins:

const CopyPlugin        = require('copy-webpack-plugin');
const CleanPlugin       = require('clean-webpack-plugin');
const UglifyJsPlugin    = require('uglifyjs-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');

// Define Base:

const BASE = '/resources/silverware/silverware';

// Define Paths:

const PATHS = {
  ADMIN: {
    SRC: path.resolve(__dirname, 'admin/client/src'),
    DIST: path.resolve(__dirname, 'admin/client/dist'),
    PUBLIC: BASE + '/admin/client/dist/'
  },
  MODULE: {
    SRC: path.resolve(__dirname, 'client/src'),
    DIST: path.resolve(__dirname, 'client/dist'),
    PUBLIC: BASE + '/client/dist/',
  },
  MODULES: path.resolve(__dirname, 'node_modules')
};

// Define Configs:

const CONFIGS = [
  {
    paths: PATHS.ADMIN,
    entry: {
      'bundle':  'bundles/bundle.js',
      'preview': 'bundles/preview.js'
    },
    plugins: [
      new CopyPlugin([
        { context: PATHS.ADMIN.SRC, from: 'images/icons', to: 'images/icons' }
      ])
    ],
    resolve: {
      alias: {
        'silverstripe-admin': path.resolve(process.env.PWD, '../../silverstripe/admin/client/src')
      }
    }
  },
  {
    paths: PATHS.MODULE,
    entry: {
      'bundle': 'bundles/bundle.js'
    },
    resolve: {
      alias: {
        'bootstrap': path.resolve(process.env.PWD, '../../../themes/silverware-theme/node_modules/bootstrap'),
        'silverware-theme': path.resolve(process.env.PWD, '../../../themes/silverware-theme/source')
      }
    }
  }
];

// Define Rules:

const rules = (env) => {
  
  // Answer Rules:
  
  return [
    {
      test: /\.js$/,
      use: [
        {
          loader: 'babel-loader'
        }
      ],
      exclude: [
        PATHS.MODULES
      ]
    },
    {
      test: /\.css$/,
      use: style(env)
    },
    {
      test: /\.scss$/,
      use: style(env, [
        {
          loader: 'resolve-url-loader',
          options: {
            sourceMap: true
          }
        },
        {
          loader: 'sass-loader',
          options: {
            sourceMap: true
          }
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
    },
    {
      test: /\.svg$/,
      use: [
        {
          loader: 'file-loader',
          options: {
            name: 'svg/[name].[ext]'
          }
        },
        {
          loader: 'svgo-loader',
          options: {
            plugins: [
              { removeTitle: true },
              { convertColors: { shorthex: false } },
              { convertPathData: true }
            ]
          }
        }
      ]
    },
    {
      test: /\.(ttf|eot|woff|woff2)$/,
      loader: 'file-loader',
      options: {
        name: 'fonts/[name].[ext]'
      }
    }
  ];
  
};

// Define Style Loaders:

const style = (env, extra = []) => {
  
  // Common Loaders:
  
  let loaders = [
    {
      loader: 'css-loader',
      options: {
        sourceMap: true
      }
    },
    {
      loader: 'postcss-loader',
      options: {
        config: {
          path: path.resolve(__dirname, 'postcss.config.js')
        },
        sourceMap: true
      }
    }
  ];
  
  // Merge Loaders:
  
  loaders = [...loaders, ...extra];
  
  // Answer Loaders:
  
  return (env === 'production') ? ExtractTextPlugin.extract({
    fallback: 'style-loader',
    use: loaders
  }) : [{ loader: 'style-loader' }].concat(loaders);
  
};

// Define Devtool:

const devtool = (env) => {
  return (env === 'production') ? false : 'source-map';
};

// Define Plugins:

const plugins = (env, config) => {
  
  // Common Plugins:
  
  let plugins = [
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery'
    })
  ];
  
  // Merge Plugins:
  
  if (config.plugins) {
    plugins = [...plugins, ...config.plugins];
  }
  
  // Answer Plugins:
  
  return plugins.concat(
    (env === 'production') ? [
      new CleanPlugin(
        [ config.paths.DIST ]
      ),
      new ExtractTextPlugin({
        filename: 'styles/[name].css',
        allChunks: true
      }),
      new UglifyJsPlugin({
        uglifyOptions: {
          output: {
            comments: false
          }
        }
      })
    ] : [
      
    ]
  );
  
};

// Define Resolve:

const resolve = (env, config) => {
  
  let resolve = {
    modules: [
      config.paths.SRC,
      PATHS.MODULES
    ]
  };
  
  if (config.resolve) {
    Object.assign(resolve, config.resolve);
  }
  
  return resolve;
  
};

// Define Externals:

const externals = (env, config) => {
  
  let externals = {
    jquery: 'jQuery'
  };
  
  if (config.externals) {
    Object.assign(externals, config.externals);
  }
  
  return externals;
  
};

// Define Configuration:

const config = (env, configs) => {
  
  // Define Exports:
  
  let exports = [];
  
  // Iterate Configs:
  
  for (let config of configs) {
    
    // Build Export:
    
    exports.push({
      entry: config.entry,
      output: {
        path: config.paths.DIST,
        filename: 'js/[name].js',
        publicPath: config.paths.PUBLIC
      },
      module: {
        rules: rules(env)
      },
      devtool: devtool(env),
      plugins: plugins(env, config),
      resolve: resolve(env, config),
      externals: externals(env, config)
    });
    
  }
  
  // Answer Exports:
  
  return exports;
  
};

// Define Module Exports:

module.exports = (env = {}) => {
  process.env.NODE_ENV = env.production ? 'production' : 'development';
  console.log(`Running in ${process.env.NODE_ENV} mode...`);
  return config(process.env.NODE_ENV, CONFIGS);
};
