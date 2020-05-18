import Vue from 'vue';
import RubricMegaWrapper from './RubricMegaWrapper.vue';
import MegaWrapperMenu from './MegaWrapperMenu.vue';
import ScoreRubric from './plugin';

import BootstrapVue from 'bootstrap-vue';

Vue.use(BootstrapVue);
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'vue-swatches/dist/vue-swatches.min.css';

Vue.config.productionTip = false;
Vue.use(ScoreRubric);

const EventBus = new Vue();

new Vue({
  data: { content: 'home' },
  render: function(h) {
    return h(MegaWrapperMenu, {
      props: { content: this.content },
      on: {
        onPage: (page: string) => {
          this.content = page;
          EventBus.$emit('onPage', page)
        }
      }
    })},
}).$mount('#rubrics-menu');

new Vue({
  data: { content: 'home' },
  render: function(h) {
    return h(RubricMegaWrapper, { props: { content: this.content } })
  },
  methods: {
    setContent(page: string) {
      this.content = page;
    }
  },
  created: function() {
    EventBus.$on('onPage', (page: string) => this.content = page);
  }
}).$mount('#app');
