import _Vue from 'vue'; // <-- notice the changed import
import Rubric from './Domain/Rubric';
import RubricDemoWrapper from './Views/RubricDemoWrapper.vue';
import RubricBuilderWrapper from './Views/RubricBuilderWrapper.vue';
import RubricPreviewWrapper from './Views/RubricPreviewWrapper.vue';
import RubricEntryWrapper from './Views/RubricEntryWrapper.vue';
import routers from './Router/router';

export default {
    install(Vue: typeof _Vue, options?: any): void {
        Vue.component('RubricDemoWrapper', RubricDemoWrapper);
        Vue.component('RubricBuilderWrapper', RubricBuilderWrapper);
        Vue.component('RubricPreviewWrapper', RubricPreviewWrapper);
        Vue.component('RubricEntryWrapper', RubricEntryWrapper);
        // console.log('adding method to proto');
        Vue.prototype.$getRouter = function(name: string) {
            return routers.getRouter(name);
        };
        Vue.prototype.$getRubricFromJSON = function (rubricJSON: string) {
            return Rubric.fromJSON(rubricJSON);
        };
    }
};
