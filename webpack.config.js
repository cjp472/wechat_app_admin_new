
var webpack = require('webpack');
module.exports = {
    entry: {    //入口文件路径
        '目录文件key':'目录文件路径',
        'accountView':'./resources/assets/js/admin/accountView.js' //示例
    },
    output: {    //出口文件路径
        path:  __dirname+'/public/js/admin',    //示例
        filename: '[name].js'
    },
    module: {
        loaders: [
            {
                test: /\.js$/,
                loader: 'babel-loader' }
        ]
    }
};