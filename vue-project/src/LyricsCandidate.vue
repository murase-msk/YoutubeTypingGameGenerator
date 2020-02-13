<template>
  <div id="lyricsCandidate">
    <div>検索テキストフィールド＋ボタン</div>
    <div>検索結果（画像＋一部歌詞＋選択ボタン）</div>
    <div>何もせず生成ボタン</div>
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
      thumbnail: document.getElementsByName("thumbnail")[0].value
    };
  }
};
</script>

<style></style>
