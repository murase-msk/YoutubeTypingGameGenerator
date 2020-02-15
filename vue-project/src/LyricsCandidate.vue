<template>
  <div id="lyrics-candidate" class="common-style">
    <div>
      <div><img :src="thumbnail" alt="サンプル" /></div>
      <div>{{ title }}</div>
      <input
        type="text"
        name="search-word"
        size="50"
        placeholder="検索する曲のタイトルを入れてください"
        v-model="inputSearchText"
      />
      <button @click="searchLyrics">検索</button> <br />
      <img id="now-loading" src="./assets/img/loading.gif" />
      <form action="/typingGame/selectLyrics" method="post" name="selectLyrics">
        <div class=".all-data">
          <div v-if="searchResultNum > 0">使用する歌詞を選択してください</div>
          <div v-if="searchResultNum === 0">
            使用できる歌詞が見つかりませんでした
          </div>
          <div
            v-for="(oneData, index) in searchResult"
            :key="index"
            class="one-data"
          >
            <div>{{ oneData.foundTitle }}（{{ oneData.foundArtistName }}）</div>
            <div>{{ oneData.foundIntroText }}</div>
            <input type="hidden" name="lyrics-url" :value="oneData.foundUrl" />
            <a href="javascript:document.selectLyrics.submit()"></a>
          </div>
        </div>
        <input type="submit" name="non-select" value="選択せず生成する" />
      </form>
    </div>
  </div>
</template>

<script>
import Vue from "vue";

export default {
  name: "app",
  //  components: {
  //  },
  data() {
    return {
      videoId: document.getElementsByName("videoId")[0].value,
      title: document.getElementsByName("title")[0].value,
      thumbnail: document.getElementsByName("thumbnail")[0].value,
      // 検索するテキスト.
      inputSearchText: "",
      // 検索結果.
      searchResult: "",
      // 検索結果の数
      searchResultNum: false,
      // ポート.
      port: String
    };
  },
  created() {
    // サーバからajaxでデータ取得.
    // 8083はvue dev-serverのポート.
    // 8081はapacheのGuest80番をHostにフォワーディングした先のポート.
    // 開発環境(vue dev-server)の場合 port8083なのでajaxはport8081にする.
    this.port = location.port === "8083" ? "8081" : location.port;
  },
  methods: {
    // 曲を検索して、アーティクト名、歌詞の一部を取得する.
    searchLyrics: function(event) {
      // ローディングGIF表示.
      document.getElementById("now-loading").style.visibility = "visible";

      const method = "GET";
      const url =
        location.protocol +
        "//" +
        location.hostname +
        ":" +
        this.port +
        "/typingGame/lyricsSearchApi?title=" +
        this.inputSearchText +
        "";
      fetch(url, { method })
        .then(response => {
          return response.json();
        })
        .then(json => {
          this.searchResult = json;
          this.searchResultNum = this.searchResult.length;
          //ローディングGIF非表示.
          document.getElementById("now-loading").style.visibility = "collapse";
        });
    }
  }
};
</script>

<style>
@import "./assets/css/common.css";
/** ローディング中GIF */
#now-loading {
  visibility: collapse;
}
/** 検索結果のそれぞれのデータ */
.one-data {
  padding: 0.5em 1em;
  margin: 0 auto; /* 中央寄せ */
  font-weight: bold;
  /* color: #6091d3;/*文字色*/
  /*background: #FFF;*/
  border: solid 3px #6091d3; /*線*/
  border-radius: 10px; /*角の丸み*/
  text-align: left; /*文字左寄せ*/
  width: 600px;
}
/** マウスホバーで背景色を変える */
.one-data:hover {
  background-color: #3d6e85;
}
/** リンクをdiv全体にする */
.one-data {
  position: relative;
  z-index: 1; /* 必要であればリンク要素の重なりのベース順序指定 */
}
/** リンクをdiv全体にする */
.one-data > a {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  text-indent: -999px;
  z-index: 2; /* 必要であればリンク要素の重なりのベース順序指定 */
}
/** ボタン上ではマウスポインタにする */
button,
input[type="submit"] {
  cursor: pointer;
}
</style>
