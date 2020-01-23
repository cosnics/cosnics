import _Vue from "vue"; // <-- notice the changed import
import ScoreRubricBuilder from './Components/ScoreRubricBuilder.vue';
import Rubric from "./Domain/Rubric";

export default {
    install(Vue: typeof _Vue, options?: any): void {
        Vue.component("ScoreRubricBuilder", ScoreRubricBuilder);
        console.log('adding method to proto');
        Vue.prototype.$getRubricFromJSON = function (rubricJSON: string) {
            return Rubric.fromJSON(rubricJSON);
        }
    }
};
