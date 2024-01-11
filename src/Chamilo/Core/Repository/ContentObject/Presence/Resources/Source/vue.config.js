module.exports = {
  chainWebpack: config => {
      config.module
          .rule('i18n')
          .resourceQuery(/blockType=i18n/)
          .type('javascript/auto')
          .use('i18n')
          .loader('@intlify/vue-i18n-loader');
          /*.end();*/
  },
  configureWebpack: {
  },
  devServer: {
      allowedHosts: 'all',
      host: 'localhost', //'192.168.56.102',
      port: 8080
  },
  css: {
      loaderOptions: {
          scss: {
          }
      }
  }
}
