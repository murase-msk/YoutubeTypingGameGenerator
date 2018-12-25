const path = require('path');

module.exports = {
    baseUrl: process.env.NODE_ENV === 'production'
        ? '/asset/vueDist/'
        : '/asset/vueDist/',
    outputDir: __dirname+'/../public/asset/vueDist/', // 2. 出力先
    assetsDir: './',
    filenameHashing: false,
    pages: {
        index: {
            entry: 'src/main.js', // エントリーポイント
            template: './public/index.html', //3. index.htmlテンプレート
            filename: 'index.html' // 省略可
        }
    },
    // ホットリロード.
    devServer: {
        host: '0.0.0.0',
        disableHostCheck: true,
        port: '8083',
        watchOptions: {
            poll: true
        }
    }
};
