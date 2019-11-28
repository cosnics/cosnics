const path = require('path');

module.exports = {
    entry: {
        'common': [
            path.resolve(__dirname, '../../../../node_modules/vue/dist/vue.common.prod.js'),
            path.resolve(__dirname, '../../../../node_modules/bootstrap-vue/dist/bootstrap-vue.js')
        ]
    },
    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: 'common.js'
    },
    mode: 'production'
};
