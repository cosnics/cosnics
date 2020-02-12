module.exports = {
    configureWebpack: {
        externals: {
            axios: 'axios'
        }
    },
    devServer: {
        disableHostCheck: true,
        host: '192.168.56.102', // '192.168.56.102', 'localhost'
        port: 8080
    }
};
