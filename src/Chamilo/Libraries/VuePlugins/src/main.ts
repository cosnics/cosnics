import Vue from 'vue';
import App from './App.vue';
import ScoreRubric from '@/Plugins/ScoreRubric/plugin';

import BootstrapVue from 'bootstrap-vue'

Vue.use(BootstrapVue);
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import ScoreRubricStore from "@/Plugins/ScoreRubric/ScoreRubricStore";

Vue.config.productionTip = false;
Vue.use(ScoreRubric);

new Vue({
  render: (h) => h(App),
  data: {
    "store": new ScoreRubricStore()
  }
}).$mount('#app');
