<template>
  <div id="watch" class="common-style">
    <Bookmark
      :videoId="videoId"
      :isAuth="isAuth"
      :isBookmark="isBookmark"
      :csrf_name="csrf_name"
      :csrf_value="csrf_value"
    ></Bookmark>
    <YoutubeMovie :videoId="videoId"></YoutubeMovie>
  </div>
</template>

<script>
import YoutubeMovie from "./components/YoutubeMovie.vue";
import Bookmark from "./components/Bookmark.vue";
import Vue from "vue";
import VueYoutube from "vue-youtube";

Vue.use(VueYoutube);

export default {
  name: "watch",
  components: {
    YoutubeMovie,
    Bookmark
  },
  data() {
    return {
      videoId: document.getElementsByName("videoId")[0].value,
      isAuth: Boolean(document.getElementsByName("isAuth")[0].value),
      isBookmark: Boolean(document.getElementsByName("isBookmark")[0].value),
      csrf_name: document.getElementsByName("csrf_name")[0].value,
      csrf_value: document.getElementsByName("csrf_value")[0].value
    };
  }
};
</script>

<style>
@import "./assets/css/common.css";
</style>
