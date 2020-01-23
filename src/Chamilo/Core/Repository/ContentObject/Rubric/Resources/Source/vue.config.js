module.exports = {
    configureWebpack: {
        externals: {
            axios: 'axios'
        }
    },
    devServer: {
        disableHostCheck: true,
        host: 'localhost', // '192.168.56.102', 'localhost'
        port: 8080
    }
};
