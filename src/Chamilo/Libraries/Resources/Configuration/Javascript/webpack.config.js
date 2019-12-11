// Run this by going into a terminal in this folder and execute ../../../../../../node_modules/.bin/webpack
const path = require('path');
const MergeIntoSingleFilePlugin = require('webpack-merge-and-include-globally');

module.exports = {
    entry: {
        vue: '../../../../../../node_modules/vue/dist/vue.js',
    },
    plugins: [
        new MergeIntoSingleFilePlugin({
            files: [
                {
                    src: [
                        path.resolve(__dirname, '../../../../../../node_modules/vue/dist/vue.min.js'),
                        path.resolve(__dirname, '../../../../../../node_modules/bootstrap-vue/dist/bootstrap-vue.min.js')
                    ],
                    dest: 'Common.js'
                }
            ]
        })
    ],
    mode: 'production'
};
