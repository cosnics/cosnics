import _Vue from 'vue'; // <-- notice the changed import
import Builder from './components/Builder.vue';
import Entry from './components/Entry.vue';

export default {
    install(Vue: typeof _Vue): void {
        Vue.component('PresenceBuilder', Builder);
        Vue.component('PresenceEntry', Entry);
    }
};
