/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

const path = require('path');
const webpack = require('webpack');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const { VueLoaderPlugin } = require('vue-loader');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const psRootDir = path.resolve(process.env.PWD, '../../../');
const psJsDir = path.resolve(psRootDir, 'admin-dev/themes/new-theme/js');
const psComponentsDir = path.resolve(psJsDir, 'components');
const psProductPageDir = path.resolve(psJsDir, 'product-page');

module.exports = {
  externals: {
    jquery: 'jQuery',
    prestashop: 'prestashop',
  },
  entry: {
    grid: ['./js/grid/index.js'],
    conf: ['./js/form/index.js', './scss/form/index.scss'],
  },
  output: {
    path: path.resolve(__dirname, '../../public/assets/admin/js'),
    filename: 'logviewer_[name].bundle.js',
    libraryTarget: 'window',
    library: '[name]',
    sourceMapFilename: '[name].[hash:8].map',
    chunkFilename: '[name].js',
  },
  resolve: {
    extensions: ['.js', '.vue', '.json', '.mjs', '.ts'],
    alias: {
      '@components': psComponentsDir,
      '@product-page': psProductPageDir,
      '@node_modules': path.resolve(__dirname, '../node_modules'),
      vue: 'vue/dist/vue.esm.js',
    },
  },
  module: {
    rules: [
      {
        test: /\.mjs$/,
        include: /node_modules/,
        type: 'javascript/auto',
      },
      {
        test: /\.js$/,
        include: path.resolve(__dirname, '../_dev/js/'),
        use: [
          {
            loader: 'babel-loader',
            options: {
              presets: [['@babel/preset-env', {useBuiltIns: 'usage', modules: false}]],
              plugins: ['@babel/plugin-transform-runtime'],
            },
          },
        ],
      },
      {
        test: /\.vue$/,
        loader: 'vue-loader',
      },
      {
        test: /\.ts?$/,
        loader: 'ts-loader',
        options: {
          appendTsSuffixTo: [/\.vue$/],
          onlyCompileBundledFiles: true,
        },
        exclude: /node_modules/,
      },
      {
        test: /\.css$/,
        include: path.resolve(__dirname, '../_dev/css/'),
        use: [
          'css-loader'
        ],
      },
      {
        test: /\.scss$/,
        include: /scss/,
        exclude: /js/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {
              sourceMap: true,
            },
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: true,
            },
          },
          {
            loader: 'sass-loader',
            options: {
              sourceMap: true,
            },
          },
        ],
      },
      {
        test: /\.scss$/,
        include: /js/,
        use: ['vue-style-loader', 'css-loader', 'sass-loader'],
      },
    ],
  },
  optimization: {
    splitChunks: {
      cacheGroups: {
        graphql: {
          test: /[\\/]node_modules[\\/](graphql|graphql-tag|graphql-tools|graphql-type-json)[\\/]/,
          name: 'graphql',
          chunks: 'all',
        },
        vendors: {
          // eslint-disable-next-line max-len
          test: /[\\/]node_modules[\\/](core-js|apollo-utilities|apollo-client|apollo-link|apollo-cache-inmemory|apollo-link-http|apollo-link-schema|vue|vue-apollo)[\\/]/,
          name: 'vendors',
          chunks: 'all',
        },
      },
    },
  },
  plugins: [
    new CleanWebpackPlugin({
      root: path.resolve(__dirname, '../'),
      exclude: ['theme.rtlfix'],
    }),
    new MiniCssExtractPlugin({filename: '../css/logviewer_[name].css'}),
    new webpack.ProvidePlugin({
      $: 'jquery', // needed for jquery-ui
      jQuery: 'jquery',
    }),
    new VueLoaderPlugin(),
  ],
};
