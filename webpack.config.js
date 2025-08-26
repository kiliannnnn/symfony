
const Encore = require('@symfony/webpack-encore');

Encore
  .setOutputPath('public/build/')
  .setPublicPath('/build')
  .addEntry('app', './assets/styles/app.css')
  .enablePostCssLoader()
  .enableSingleRuntimeChunk()
  // ...other options...
;

module.exports = Encore.getWebpackConfig();