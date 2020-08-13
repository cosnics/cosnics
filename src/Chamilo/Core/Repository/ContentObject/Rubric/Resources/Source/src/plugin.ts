import _Vue from 'vue'; // <-- notice the changed import
import VueI18n from 'vue-i18n';
import Rubric from './Domain/Rubric';
import RubricDemoWrapper from './Views/RubricDemoWrapper.vue';
import RubricBuilderWrapper from './Views/RubricBuilderWrapper.vue';
import RubricPreviewWrapper from './Views/RubricPreviewWrapper.vue';
import RubricEntryWrapper from './Views/RubricEntryWrapper.vue';
import routers from './Router/router';

export default {
    install(Vue: typeof _Vue, options?: any): void {
        Vue.use(VueI18n);
        Vue.component('RubricDemoWrapper', RubricDemoWrapper);
        Vue.component('RubricBuilderWrapper', RubricBuilderWrapper);
        Vue.component('RubricPreviewWrapper', RubricPreviewWrapper);
        Vue.component('RubricEntryWrapper', RubricEntryWrapper);
        Vue.prototype.$createI18nInstance = function(lang: string|undefined) {
            return new VueI18n({ locale: lang });
        };
        // console.log('adding method to proto');
        Vue.prototype.$getRouter = function(name: string) {
            return routers.getRouter(name);
        };
        Vue.prototype.$getRubricFromJSON = function (rubricJSON: string) {
            return Rubric.fromJSON(rubricJSON);
        };
    }
};
