import Vue from 'vue';
import App from './App.vue';
import ScoreRubric from './plugin';

import BootstrapVue from 'bootstrap-vue'

Vue.use(BootstrapVue);
import 'jquery.fancytree/dist/skin-lion/ui.fancytree.css';  // CSS or LESS
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import ScoreRubricStore from "./ScoreRubricStore";

Vue.config.productionTip = false;
Vue.use(ScoreRubric);

new Vue({
  render: (h) => h(App),
  data: {
    "store": new ScoreRubricStore()
  }
}).$mount('#app');
