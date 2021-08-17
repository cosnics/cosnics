import _Vue from 'vue'; // <-- notice the changed import
import Main from './components/Main.vue';

export default {
    install(Vue: typeof _Vue, options?: any): void {
        Vue.component('PresenceMainApp', Main);
    }
};
