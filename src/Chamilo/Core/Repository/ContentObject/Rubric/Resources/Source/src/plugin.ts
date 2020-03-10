import _Vue from "vue"; // <-- notice the changed import
import ScoreRubricBuilder from './Components/ScoreRubricBuilder.vue';
import ScoreRubricTreeBuilder from "./Components/TreeBuilder/ScoreRubricTreeBuilder.vue";
import Rubric from "./Domain/Rubric";
import ScoreRubricView from "./Components/View/ScoreRubricView.vue";

export default {
    install(Vue: typeof _Vue, options?: any): void {
        Vue.component("ScoreRubricBuilder", ScoreRubricBuilder);
        Vue.component("ScoreRubricTreeBuilder", ScoreRubricTreeBuilder);
        Vue.component("ScoreRubricView", ScoreRubricView);
        console.log('adding method to proto');
        Vue.prototype.$getRubricFromJSON = function (rubricJSON: string) {
            return Rubric.fromJSON(rubricJSON);
        }
    }
};
