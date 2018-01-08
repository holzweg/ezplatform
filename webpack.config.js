const path = require('path')
const ExtractTextPlugin = require('extract-text-webpack-plugin')

const extractSass = new ExtractTextPlugin({
  filename: './dist/[name].bundle.css',
  disable: process.env.NODE_ENV === 'development'
})

module.exports = {
  entry: [
    './src/AppBundle/Resources/public/js/index.js',
    './src/AppBundle/Resources/public/scss/main.scss'
  ],
  output: {
    path: path.resolve(__dirname, './src/AppBundle/Resources/public/dist'),
    filename: "bundle.js",
  },
  devServer: {
    inline: true,
    host: '0.0.0.0',
    port: 8080,
    headers: { 'Access-Control-Allow-Origin': '*' }
  },
  module: {
    loaders: [
      {
        test: /\.js$/,
        loader: 'babel-loader',
        query: {
          presets: ['es2015']
        }
      }
    ],
    rules: [
      {
        test: /\.scss$/,
        use: extractSass.extract({
          use: [
            {
              loader: 'css-loader'
            },
            {
              loader: 'sass-loader'
            }
          ],
          fallback: 'style-loader'
        })
      }
    ]
  },
  stats: {
    colors: true
  },
  devtool: 'source-map',
  plugins: [extractSass],
  resolve: {
    alias: {
      vue: 'vue/dist/vue.js'
    }
  }
}
