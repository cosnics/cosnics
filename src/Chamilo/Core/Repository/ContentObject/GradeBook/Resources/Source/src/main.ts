import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import Vue from 'vue';
import App from './App.vue';
import UserScoresApp from './UserScoresApp.vue';
import VueI18n from 'vue-i18n';
import BootstrapVue from 'bootstrap-vue';
Vue.use(VueI18n);

Vue.use(BootstrapVue);

Vue.config.productionTip = false;
(window as any).App = App;
(window as any).UserScoresApp = UserScoresApp;
(window as any).VueI18n = VueI18n;

/*new Vue({
  render: h => h(App),
}).$mount('#app');*/
