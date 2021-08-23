import _Vue from 'vue'; // <-- notice the changed import
import Main from './components/Main.vue';
import Entry from './components/Entry.vue';

export default {
    install(Vue: typeof _Vue, options?: any): void {
        Vue.component('PresenceBuilder', Main);
        Vue.component('PresenceEntry', Entry);
    }
};
