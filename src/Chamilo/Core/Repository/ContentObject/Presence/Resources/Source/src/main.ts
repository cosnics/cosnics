import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import Vue from 'vue';
import App from './App.vue';
import BootstrapVue from 'bootstrap-vue';
import { makeServer } from "./server"

if (process.env.NODE_ENV === "development") {
  makeServer();
}
Vue.use(BootstrapVue);

Vue.config.productionTip = false;

new Vue({
  render: h => h(App),
}).$mount('#app');
