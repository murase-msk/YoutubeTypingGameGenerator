module.exports = {
  // baseUrl: process.env.NODE_ENV === 'production'
  //     ? '/asset/vueDist/'
  //     : '/asset/vueDist/',
  outputDir: __dirname + "/../public/asset/vueDist/", // 2. 出力先
  assetsDir: "./",
  filenameHashing: false,
  pages: {
    watch: {
      entry: "src/watch.js", // エントリーポイント
      template: "./public/watch.html", //3. index.htmlテンプレート
      filename: "watch.html" // outputFilename 省略可
    },
    edit: {
      entry: "src/edit.js", // エントリーポイント
      template: "./public/edit.html", //3. index.htmlテンプレート
      filename: "edit.html" // 省略可
    },
    lyricsCandidate: {
      entry: "src/lyricsCandidate.js", // エントリーポイント
      template: "./public/lyricsCandidate.html", //3. index.htmlテンプレート
      filename: "lyricsCandidate.html" // 省略可
    }
  },
  // ホットリロード.
  devServer: {
    host: "0.0.0.0",
    disableHostCheck: true,
    port: "8083",
    watchOptions: {
      poll: true
    },
    historyApiFallback: {
      rewrites: [
        { from: /\/watch/, to: "/watch.html" }, // watchwatch.html に飛ばす
        { from: /\/edit/, to: "/edit.html" },
        { from: /\/lyricsCandidate/, to: "/lyricsCandidate.html" }
      ]
    }
  },
  // vscode デバッグ用.
  configureWebpack: {
    devtool: "source-map"
  }
};
