import Vue from "vue";
import Watch from "./Watch.vue";

Vue.config.productionTip = false;

new Vue({
  render: h => h(Watch)
}).$mount("#watch");
