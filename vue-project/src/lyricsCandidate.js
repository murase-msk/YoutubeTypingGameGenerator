import Vue from "vue";
import Edit from "./LyricsCandidate.vue";

Vue.config.productionTip = false;

new Vue({
  render: h => h(Edit)
}).$mount("#lyricsCandidate");
