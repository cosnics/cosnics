<template>
    <div class="container-fluid">
        <rubric-entry v-if="rubric" :rubric="rubric" :criterium-evaluations="getCriteriumEvaluations(evaluator)" :ui-state="store.uiState.entry" :options="store.uiState.entry.options"
                      @level-selected="selectLevel" @criterium-feedback-changed="updateCriteriumFeedback">
            <template v-slot:demoEvaluator>
                <li class="app-header-item">Demo:
                    <select v-model="evaluator" @change="store.uiState.entry.options.evaluator = $event.target.value">
                        <option disabled value="">Selecteer</option>
                        <option v-for="evaluator in store.rubricResults.evaluators">{{evaluator}}</option>
                    </select>
                </li>
            </template>
        </rubric-entry>
    </div>
</template>

<script lang="ts">
    import {Component, Vue} from 'vue-property-decorator';
    import RubricEntry from './RubricEntry.vue';
    import store from '../store';
    import Criterium from '../Domain/Criterium';
    import Level from '../Domain/Level';
    import Rubric, {RubricJsonObject} from '../Domain/Rubric';
    import {CriteriumEvaluation} from '../Domain/util';

    interface EvaluatorEvaluations {
        name: string;
        criteriumEvaluations: CriteriumEvaluation[];
    }

    @Component({
        components: {
            RubricEntry
        },
    })
    export default class RubricEntryWrapper extends Vue {
        private rubric: Rubric | undefined;
        private store: any = store;
        private evaluatorEvaluations: EvaluatorEvaluations[] = [];
        private evaluator: string = '';

        getCriteriumEvaluations(name: string): CriteriumEvaluation[] {
            if (!name) { return []; }
            const evaluatorEvaluation = this.evaluatorEvaluations.find(evaluatorEvaluation => evaluatorEvaluation.name === name);
            if (!evaluatorEvaluation) { throw new Error(`No evaluation data found for evaluator: ${name}`); }
            return evaluatorEvaluation.criteriumEvaluations;
        }

        selectLevel(criterium: Criterium, level: Level) {
            if (!this.evaluator) { return; }
            const evaluations = (store.rubricResults.evaluations as any)[this.evaluator];
            const evaluation = evaluations.find((evaluation: any) => evaluation.criteriumId === criterium.id);
            if (!evaluation) {
                evaluations.push({ criteriumId: criterium.id, levelId: level.id, feedback: ''});
            } else {
                evaluation.levelId = level.id;
            }
        }

        updateCriteriumFeedback(criterium: Criterium, feedback: string) {
            if (!this.evaluator) { return; }
            const evaluations = (store.rubricResults.evaluations as any)[this.evaluator];
            const evaluation = evaluations.find((evaluation: any) => evaluation.criteriumId === criterium.id);
            evaluation.feedback = feedback;
        }

        initData() {
            const rubric = this.rubric = Rubric.fromJSON(this.store.rubricData as RubricJsonObject);
            const defaultLevel = rubric.levels.find(level => level.isDefault) || null;
            const rubricDefaultEvaluation: CriteriumEvaluation[] = rubric.getAllCriteria().map(criterium =>
                ({ criterium, level: defaultLevel, score: defaultLevel ? rubric.getChoiceScore(criterium, defaultLevel) : 0, feedback: '' })
            );
            const evaluators = this.store.rubricResults.evaluators;
            this.evaluator = store.uiState.entry.options.evaluator;
            this.evaluatorEvaluations = evaluators.map((evaluator: any) => {
                const evaluations = this.store.rubricResults.evaluations[evaluator];
                const criteriumEvaluations: CriteriumEvaluation[] = rubricDefaultEvaluation.map(defaultCriteriumEvaluation => {
                    const storeEvaluation = evaluations.find((evaluation: any) => evaluation.criteriumId === defaultCriteriumEvaluation.criterium.id);
                    if (storeEvaluation) {
                        const level = rubric.levels.find(level => level.id === storeEvaluation.levelId);
                        return { criterium: defaultCriteriumEvaluation.criterium, level: level!, score: rubric.getChoiceScore(defaultCriteriumEvaluation.criterium, level!), feedback: storeEvaluation.feedback };
                    } else {
                        return { ...defaultCriteriumEvaluation };
                    }
                });
                return {name: evaluator, criteriumEvaluations};
            });
        }

        created() {
            this.initData();
        }
    }
</script>
