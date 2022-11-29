import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import Vue from 'vue';
import App from './App.vue';
import ImporterApp from './ImporterApp.vue';
import UserScoresApp from './UserScoresApp.vue';
import VueI18n from 'vue-i18n';
import BootstrapVue from 'bootstrap-vue';

declare global {
    interface Window {
        App: Object,
        ImporterApp: Object,
        UserScoresApp: Object,
        VueI18n: Object
    }
}

Vue.use(VueI18n);
Vue.use(BootstrapVue);
Vue.config.productionTip = false;
Object.assign(window, {App, ImporterApp, UserScoresApp, VueI18n});

/*new Vue({
  render: h => h(App),
}).$mount('#app');*/
