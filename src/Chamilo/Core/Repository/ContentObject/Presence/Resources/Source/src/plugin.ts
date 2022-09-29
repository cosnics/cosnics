import _Vue from 'vue'; // <-- notice the changed import
import Builder from './components/Builder.vue';
import Entry from './components/Entry.vue';
import UserEntry from './components/UserEntry.vue';
import Properties from './components/Properties.vue';

export default {
    install(Vue: typeof _Vue): void {
        Vue.component('PresenceBuilder', Builder);
        Vue.component('PresenceEntry', Entry);
        Vue.component('PresenceUserEntry', UserEntry);
        Vue.component('PresenceProperties', Properties);
    }
};
