<template>
  <div id="edit"><EditScript :videoId="videoId"></EditScript></div>
</template>

<script>
import EditScript from "./components/EditScript.vue";
import Vue from "vue";
import VueYoutube from "vue-youtube";

Vue.use(VueYoutube);

export default {
  name: "app",
  components: {
    EditScript
  },
  data() {
    return {
      videoId: document.getElementsByName("videoId")[0].value
    };
  }
};
</script>

<style>
#edit {
  font-family: "Avenir", Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
