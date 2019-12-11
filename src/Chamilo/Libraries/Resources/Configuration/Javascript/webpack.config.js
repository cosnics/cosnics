// Run this by going into a terminal in this folder and execute ../../../../../../node_modules/.bin/webpack
const path = require('path');

module.exports = {
    entry: {
        'Common': [
            path.resolve(__dirname, '../../../../../../node_modules/vue/dist/vue.js'),
            path.resolve(__dirname, '../../../../../../node_modules/bootstrap-vue/dist/bootstrap-vue.js')
        ],
        // Automatically add it to the web folder by creating a new entry with a relative path to the web folder
        '../../../../../web/Chamilo/Libraries/Resources/Javascript/Common': [
            path.resolve(__dirname, '../../../../../../node_modules/vue/dist/vue.js'),
            path.resolve(__dirname, '../../../../../../node_modules/bootstrap-vue/dist/bootstrap-vue.js')
        ]
    },
    output: {
        path: path.resolve(__dirname, '../../Javascript'),
        filename: '[name].js'
    },
    mode: 'production',
    // Exclude vue to be an automatic dependency because we inject it manually. This is automatically found due to bootstrap-vue requiring vue in his dist folder
    // Expand this if other libraries show the same behavior
    externals: {
        // 'vue': 'vue',
    },
    optimization: {
        minimize: false
    },
};
