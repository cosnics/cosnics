import Vue from 'vue';
import App from './App2.vue';
import ScoreRubric from './plugin';

import BootstrapVue from 'bootstrap-vue';

Vue.use(BootstrapVue);
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'vue-swatches/dist/vue-swatches.min.css';
import ScoreRubricStore from './ScoreRubricStore';
import { makeServer } from './server';

if (process.env.NODE_ENV === "development") {
  makeServer();
}

Vue.config.productionTip = false;
Vue.use(ScoreRubric);

new Vue({
  render: h => h(App),
  data: {
    "store": new ScoreRubricStore()
  }
}).$mount('#app');
