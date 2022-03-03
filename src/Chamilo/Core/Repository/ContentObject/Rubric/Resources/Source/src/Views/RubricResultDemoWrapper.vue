<template>
    <div id="app" style="margin-top: -20px">
        <rubric-result v-if="rubric" :rubric="rubric" :rubric-evaluation="rubricEvaluation" :options="{ isDemo: true }"></rubric-result>
    </div>
</template>

<script lang="ts">
    import {Component, Vue} from 'vue-property-decorator';
    import Rubric, {RubricJsonObject} from '../Domain/Rubric';
    import Criterium from '../Domain/Criterium';
    import RubricEvaluation from '../Domain/RubricEvaluation';
    import RubricResult from './RubricResult.vue';
    import {TreeNodeEvaluation} from '../Util/interfaces';
    import store from '../store';

    @Component({
        components: {
            RubricResult
        },
    })
    export default class RubricResultDemoWrapper extends Vue {
        private rubric: Rubric | undefined;
        private store: any = store;
        private rubricEvaluation: RubricEvaluation | undefined;

        initData() {
            const rubric = Rubric.fromJSON(this.store.rubricData as RubricJsonObject);
            const results = this.store.rubricResults;
            const evaluators = results.evaluators;
            const treeNodeResults = this.processTreeNodeResults(rubric, results, evaluators);
            this.rubric = rubric;
            this.rubricEvaluation = RubricEvaluation.fromResults(this.rubric, evaluators, treeNodeResults);
        }

        private processTreeNodeResults(rubric: Rubric, results: any, evaluators: any) {
            const defaultLevel = rubric.levels.find(level => level.isDefault) || null;
            return rubric.getAllTreeNodes().map(treeNode => {
                const defaultEvaluation = {
                    treeNode,
                    level: defaultLevel,
                    score: treeNode instanceof Criterium ? (defaultLevel ? rubric.getChoiceScore(treeNode, defaultLevel) : 0) : null,
                    feedback: ''
                };
                const evaluations = evaluators.map((evaluator: any) => {
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
                    return {evaluator, treeNodeEvaluation};
                });
                return {treeNode, evaluations};
            });
        }

        created() {
            this.initData();
        }
    }
</script>
