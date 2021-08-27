import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import Vue from 'vue';
import App from './App.vue';
import BootstrapVue from 'bootstrap-vue';
import { makeServer } from "./server"
import VueI18n from 'vue-i18n';
Vue.use(VueI18n);

if (process.env.NODE_ENV === "development") {
  makeServer();
}
Vue.use(BootstrapVue);

Vue.config.productionTip = false;

const lang = document.querySelector('html')!.getAttribute('lang');

new Vue({
  i18n: new VueI18n({ locale: 'nl' || undefined }),
  render: h => h(App),
}).$mount('#app');
