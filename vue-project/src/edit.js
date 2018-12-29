import Vue from "vue";
import Edit from "./Edit.vue";

Vue.config.productionTip = false;

new Vue({
  render: h => h(Edit)
}).$mount("#edit");
