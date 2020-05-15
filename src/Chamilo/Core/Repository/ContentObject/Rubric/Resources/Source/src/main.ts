import Vue from 'vue';
//import RubricEntryWrapper from './RubricEntryWrapper.vue';
//import RubricResultWrapper from './RubricResultWrapper.vue';
//import RubricBuilderFullWrapper from './RubricBuilderFullWrapper.vue';
//import RubricMegaWrapper from './RubricMegaWrapper.vue';
import ScoreRubric from './plugin';

import BootstrapVue from 'bootstrap-vue';

Vue.use(BootstrapVue);
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'vue-swatches/dist/vue-swatches.min.css';
import RubricEntry from "./RubricEntry.vue";

Vue.config.productionTip = false;
Vue.use(ScoreRubric);

function getParameterByName(name: string, url: string = window.location.href) {
  name = name.replace(/[\[\]]/g, '\\$&');
  var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
      results = regex.exec(url);
  if (!results) return null;
  if (!results[2]) return '';
  return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

new Vue({
  render: function(h) {
      const content = getParameterByName('content');
      if (content === 'builder') {
        const RubricBuilderWrapper = require('./RubricBuilderWrapper.vue');
        return h(RubricBuilderWrapper.default);
      } else if (content === 'builder-full') {
        const RubricBuilderFullWrapper = require('./RubricBuilderFullWrapper.vue');
        return h(RubricBuilderFullWrapper.default);
      } else if (content === 'entry') {
        const RubricEntryWrapper = require('./RubricEntryWrapper.vue');
        return h(RubricEntryWrapper.default);
      } else if (content === 'results') {
        const RubricResultWrapper = require('./RubricResultWrapper.vue');
        return h(RubricResultWrapper.default);
      } else {
        const RubricMegaWrapper = require('./RubricMegaWrapper.vue');
        return h(RubricMegaWrapper.default);
      }
  }
}).$mount('#app');
