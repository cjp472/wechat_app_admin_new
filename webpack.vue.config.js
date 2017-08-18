var webpack = require('webpack');


module.exports = {
    watch: true, //监听变化自动编译
    entry: {
        //模板内vue组件入口
        '/public/vue/shopDiy/dist/shopDiyIndex': './public/vue/shopDiy/shopDiyIndex.js', //一对多专栏列表页
    },
    output: {
        path: './',
        filename: '[name].min.js'
    },
    module: {
        loaders: [{
                test: /\.vue$/,
                //解析vue模板
                loader: "vue-loader"
            },
            {
                test: /\.js$/,
                exclude: /(node_modules\/vue-lazyload|node_modules\/.*.*.*@vue-lazyload)/,
                //js转换
                loader: "babel-loader",
                query: {
                    presets: ['es2015']
                }
            },
            {
                test: /\.css$/,
                //css转换
                loader: 'vue-style!css'
            }
        ]
    },
    vue: {
        loaders: {
            css: 'vue-style!css',
        }
    },
    plugins: [

    ],
    resolve: {
        alias: {
            'vue$': 'vue/dist/vue.common.js'
        }
    }
};
