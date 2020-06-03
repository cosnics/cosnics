import Vue from 'vue';

import RubricMegaWrapper from './Views/RubricDemoWrapper.vue';
import MegaWrapperMenu from './Components/MegaWrapperMenu.vue';
import ScoreRubric from './plugin';
import router from './Router/router';

import BootstrapVue from 'bootstrap-vue';

Vue.use(BootstrapVue);

import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'vue-swatches/dist/vue-swatches.min.css';

Vue.config.productionTip = false;
Vue.use(ScoreRubric);

new Vue({
  router,
  render: function(h) {
    return h(MegaWrapperMenu)
  },
}).$mount('#rubrics-menu');

new Vue({
  router,
  render: function(h) {
    return h(RubricMegaWrapper)
  }
}).$mount('#app');
