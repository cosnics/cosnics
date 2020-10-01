<template>
    <div style="margin-top: -20px">
        <rubric-result v-if="rubric" :rubric="rubric" :evaluators="store.rubricResults.evaluators" :tree-node-results="treeNodeResults" :options="{ isDemo:  true }"></rubric-result>
    </div>
</template>

<script lang="ts">
    import {Component, Vue} from 'vue-property-decorator';
    import Rubric, {RubricJsonObject} from '../Domain/Rubric';
    import RubricResult from './RubricResult.vue';
    //import RubricResult from './RubricResult2.vue';
    import store from '../store';
    //import store from '../store2';
    import {TreeNodeEvaluation, TreeNodeResult} from '../Util/interfaces';
    import Criterium from "../Domain/Criterium";

    @Component({
        components: {
            RubricResult
        },
    })
    export default class RubricResultDemoWrapper extends Vue {
        private rubric: Rubric | undefined;
        private store: any = store;
        private treeNodeResults: TreeNodeResult[] = [];

        initData() {
            const rubric = Rubric.fromJSON(this.store.rubricData as RubricJsonObject);
            const results = this.store.rubricResults;
            const evaluators = results.evaluators;
            const defaultLevel = rubric.levels.find(level => level.isDefault) || null;

            this.treeNodeResults = rubric.getAllTreeNodes().map(treeNode => {
                const defaultEvaluation = { treeNode, level: defaultLevel, score: treeNode instanceof Criterium ? (defaultLevel ? rubric.getChoiceScore(treeNode, defaultLevel) : 0) : null, feedback: '' };
                const evaluations = evaluators.map((evaluator : any) => {
                    const treeNodeEvaluation: TreeNodeEvaluation = {...defaultEvaluation};
                    const evaluations = results.evaluations[evaluator.userId];
                    const treeNodeEvaluationInput = evaluations.find((o: any) => o.treeNodeId === treeNode.id);
                    if (treeNodeEvaluationInput) {
                        treeNodeEvaluation.feedback = treeNodeEvaluationInput.feedback;
                        const chosenLevel = rubric.levels.find(level => level.id === treeNodeEvaluationInput.levelId);
                        if (chosenLevel) {
                            treeNodeEvaluation.level = chosenLevel;
                            if (treeNode instanceof Criterium) {
                                treeNodeEvaluation.score = rubric.getChoiceScore(treeNode, chosenLevel);
                            }
                        }
                    }
                    return { evaluator, treeNodeEvaluation };
                });
                return { treeNode, evaluations };
              });
              this.rubric = rubric;
        }

        created() {
            this.initData();
        }
    }
</script>
