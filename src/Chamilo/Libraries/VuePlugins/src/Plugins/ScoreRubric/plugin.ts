import _Vue from "vue"; // <-- notice the changed import
import ScoreRubricBuilder from '@/Plugins/ScoreRubric/Components/ScoreRubricBuilder.vue';
import Rubric from "@/Plugins/ScoreRubric/Domain/Rubric";

export default function ScoreRubric(Vue: typeof _Vue, options?: any): void {
    Vue.component("ScoreRubricBuilder", ScoreRubricBuilder);
    Vue.prototype.$getRubricFromJSON = function (rubricJSON: string) {
            return Rubric.fromJSON(rubricJSON);
    }
}
