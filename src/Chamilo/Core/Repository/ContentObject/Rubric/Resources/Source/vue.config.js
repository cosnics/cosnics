module.exports = {
    chainWebpack: config => {
        config.module
            .rule('i18n')
            .resourceQuery(/blockType=i18n/)
            .type('javascript/auto')
            .use('i18n')
            .loader('@kazupon/vue-i18n-loader');
            /*.end();*/
    },
    configureWebpack: {
    },
    devServer: {
        disableHostCheck: true,
        host: 'localhost', //'192.168.56.102',
        port: 8080
    },
    css: {
        loaderOptions: {
            scss: {
                prependData: `@import "@/scss/all.scss";`
            }
        }
    }
};
