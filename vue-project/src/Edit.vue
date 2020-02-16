<template>
  <div id="edit" class="common-style">
    <EditScript :videoId="videoId"></EditScript>
  </div>
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
@import "./assets/css/common.css";
</style>
