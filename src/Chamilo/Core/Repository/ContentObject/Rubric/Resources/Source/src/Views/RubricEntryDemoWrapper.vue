<i18n>
{
    "en": {
        "select": "Select"
    },
    "fr": {
        "select": "Sélectionnez"
    },
    "nl": {
        "select": "Selecteer"
    }
}
</i18n>

<template>
    <div style="margin-top: -20px">
        <rubric-entry :rubric="rubric" :rubric-evaluation="getRubricEvaluation(evaluator)" :ui-state="store.uiState.entry" :options="store.uiState.entry.options"
                      @level-selected="selectLevel" @criterium-feedback-changed="updateFeedback">
            <template v-slot:demoEvaluator>
                <li class="app-tool-item">Demo:
                    <select v-model="evaluator" @change="store.uiState.entry.options.evaluator = evaluator">
                        <option disabled :value="null">{{ $t('select') }}</option>
                        <option v-for="evaluator in store.rubricResults.evaluators" :value="evaluator">{{evaluator.name}}</option>
                    </select>
                </li>
            </template>
        </rubric-entry>
    </div>
</template>

<script lang="ts">
    import {Component, Vue} from 'vue-property-decorator';
    import Rubric, {RubricJsonObject} from '../Domain/Rubric';
    import TreeNode from '../Domain/TreeNode';
    import Criterium from '../Domain/Criterium';
    import Level from '../Domain/Level';
    import RubricEvaluation from '../Domain/RubricEvaluation';
    import RubricEntry from './RubricEntry.vue';
    import {TreeNodeEvaluation} from '../Util/interfaces';
    import store from '../store';

    interface EvaluatorEvaluations {
        evaluator: any;
        treeNodeEvaluations: TreeNodeEvaluation[];
    }

    @Component({
        components: {
            RubricEntry
        },
    })
    export default class RubricEntryDemoWrapper extends Vue {
        private rubric: Rubric | undefined;
        private store: any = store;
        private evaluatorEvaluations: EvaluatorEvaluations[] = [];
        private evaluator: any = null;

        getTreeNodeEvaluations(evaluator: any): TreeNodeEvaluation[] {
            if (!evaluator) { return []; }
            const evaluatorEvaluation = this.evaluatorEvaluations.find(evaluatorEvaluation => evaluatorEvaluation.evaluator.userId === evaluator.userId);
            if (!evaluatorEvaluation) { throw new Error(`No evaluation data found for evaluator: ${evaluator.name}`); }
            return evaluatorEvaluation.treeNodeEvaluations;
        }

        getRubricEvaluation(evaluator: any): RubricEvaluation {
            if (!this.rubric) { throw new Error('Rubric hasn\'t fully loaded yet'); }
            return RubricEvaluation.fromEntry(this.rubric, this.getTreeNodeEvaluations(evaluator));
        }

        selectLevel(treeNode: TreeNode, level: Level) {
            if (!this.evaluator) { return; }
            const evaluations = (store.rubricResults.evaluations as any)[this.evaluator.userId];
            const evaluation = evaluations.find((evaluation: any) => evaluation.treeNodeId === treeNode.id);
            if (!evaluation) {
                evaluations.push({ treeNodeId: treeNode.id, levelId: level.id, feedback: ''});
            } else {
                evaluation.levelId = level.id;
            }
        }

        updateFeedback(treeNode: TreeNode, feedback: string) {
            if (!this.evaluator) { return; }
            const evaluations = (store.rubricResults.evaluations as any)[this.evaluator.userId];
            const evaluation = evaluations.find((evaluation: any) => evaluation.treeNodeId === treeNode.id);
            if (!evaluation) {
                evaluations.push({ treeNodeId: treeNode.id, levelId: this.rubric!.levels.find(level => level.isDefault)?.id || null, feedback });
            } else {
                evaluation.feedback = feedback;
            }
        }

        initData() {
            const rubric = this.rubric = Rubric.fromJSON(this.store.rubricData as RubricJsonObject);
            const defaultLevel = rubric.levels.find(level => level.isDefault) || null;
            const rubricDefaultEvaluation: TreeNodeEvaluation[] = rubric.getAllTreeNodes().map(treeNode =>
                ({ treeNode, level: treeNode instanceof Criterium ? defaultLevel : null, score: treeNode instanceof Criterium ? (defaultLevel ? rubric.getChoiceScore(treeNode, defaultLevel) : 0) : null, feedback: '' })
            );
            const evaluators = this.store.rubricResults.evaluators;
            this.evaluator = this.store.uiState.entry.options.evaluator;
            this.evaluatorEvaluations = evaluators.map((evaluator: any) => {
                const evaluations = this.store.rubricResults.evaluations[evaluator.userId];
                const treeNodeEvaluations: TreeNodeEvaluation[] = rubricDefaultEvaluation.map(defaultCriteriumEvaluation => {
                    const storeEvaluation = evaluations.find((evaluation: any) => evaluation.treeNodeId === defaultCriteriumEvaluation.treeNode.id);
                    if (storeEvaluation) {
                        const level = rubric.levels.find(level => level.id === storeEvaluation.levelId) || null;
                        const score = (level && defaultCriteriumEvaluation.treeNode instanceof Criterium) ? rubric.getChoiceScore(defaultCriteriumEvaluation.treeNode, level) : null;
                        return { treeNode: defaultCriteriumEvaluation.treeNode, level, score, feedback: storeEvaluation.feedback };
                    } else {
                        return { ...defaultCriteriumEvaluation };
                    }
                });
                return { evaluator, treeNodeEvaluations };
            });
        }

        created() {
            this.initData();
        }
    }
</script>
