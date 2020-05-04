import _Vue from 'vue'; // <-- notice the changed import
import Rubric from './Domain/Rubric';

export default {
    install(Vue: typeof _Vue, options?: any): void {
        // console.log('adding method to proto');
        Vue.prototype.$getRubricFromJSON = function (rubricJSON: string) {
            return Rubric.fromJSON(rubricJSON);
        }
    }
};
