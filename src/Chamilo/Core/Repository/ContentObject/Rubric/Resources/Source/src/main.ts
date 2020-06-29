import Vue from 'vue';

import RubricDemoWrapper from './Views/RubricDemoWrapper.vue';
import MegaWrapperMenu from './Components/MegaWrapperMenu.vue';
import ScoreRubric from './plugin';
import router from './Router/router';

import BootstrapVue from 'bootstrap-vue';

Vue.use(BootstrapVue);
import VueI18n from 'vue-i18n';
Vue.use(VueI18n);

import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'vue-swatches/dist/vue-swatches.css';

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
    return h(RubricDemoWrapper)
  }
}).$mount('#app');
