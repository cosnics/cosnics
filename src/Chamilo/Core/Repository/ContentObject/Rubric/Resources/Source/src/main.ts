import Vue from 'vue';
import RubricMegaWrapper from './RubricMegaWrapper.vue';
import ScoreRubric from './plugin';

import BootstrapVue from 'bootstrap-vue';

Vue.use(BootstrapVue);
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'vue-swatches/dist/vue-swatches.min.css';

Vue.config.productionTip = false;
Vue.use(ScoreRubric);

new Vue({
  render: h => h(RubricMegaWrapper)
}).$mount('#app');
