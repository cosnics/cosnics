const path = require('path');

module.exports = {
    vendorPath: path.resolve(__dirname, '../../../node_modules'),
    entry: {
        'common.js': [
            path.resolve(this.vendorPath, 'vue/dist/vue.common.prod.js'),
            path.resolve(this.vendorPath, 'bootstrap-vue/dist/bootstrap-vue.js')
        ]
    }
};
