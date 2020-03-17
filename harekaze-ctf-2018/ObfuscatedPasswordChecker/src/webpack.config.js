'use strict';

const path = require('path');
const JavaScriptObfuscator = require('webpack-obfuscator');

module.exports = {
    entry: {
        'bundle': './index.js',
    },
    output: {
        // path: __dirname + '/dist',
        path: path.resolve(__dirname, 'dist'),
        filename: '[name].js'
    },
    devServer: {
        contentBase: 'dist',
        port: '3000'
    },
    plugins: [
        new JavaScriptObfuscator({
            deadCodeInjection: true,
            deadCodeInjectionThreshold: 1,
            debugProtection: true,
            rotateUnicodeArray: true,
            selfDefending: true,
            stringArray: true,
            stringArrayThreshold: 1,
            stringArrayEncoding: 'rc4'
        }, ['bundle'])
    ]
};