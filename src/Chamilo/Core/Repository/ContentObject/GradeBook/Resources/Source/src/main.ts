import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import Vue from 'vue';
import App from './App.vue';
import BootstrapVue from 'bootstrap-vue';

Vue.use(BootstrapVue);

Vue.config.productionTip = false;

(window as any).App = App;

/*new Vue({
  render: h => h(App),
}).$mount('#app');

Vue.component('GradeBookApp', App);*/