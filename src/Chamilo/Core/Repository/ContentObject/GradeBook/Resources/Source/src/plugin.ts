import _Vue from 'vue'; // <-- notice the changed import
import App from './App.vue';
import UserScoresApp from './UserScoresApp.vue';

export default {
    install(Vue: typeof _Vue, options?: any): void {
        Vue.component('GradeBookApp', App);
        Vue.component('GradeBookUserScoresApp', UserScoresApp);
    }
};