
const Encore = require('@symfony/webpack-encore');

Encore
  .setOutputPath('public/build/')
  .setPublicPath('/build')
  .addEntry('app', './assets/app.js')
  .enablePostCssLoader()
  .enableSingleRuntimeChunk()
;

module.exports = Encore.getWebpackConfig();