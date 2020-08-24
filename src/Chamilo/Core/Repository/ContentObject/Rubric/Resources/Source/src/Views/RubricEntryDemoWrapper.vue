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
        <rubric-entry :rubric="rubric" :criterium-evaluations="getCriteriumEvaluations(evaluator)" :ui-state="store.uiState.entry" :options="store.uiState.entry.options"
                      @level-selected="selectLevel" @criterium-feedback-changed="updateCriteriumFeedback">
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
    import Level from '../Domain/Level';
    import Criterium from '../Domain/Criterium';
    import RubricEntry from './RubricEntry.vue';
    import {CriteriumEvaluation} from '../Util/interfaces';
    import store from '../store';

    interface EvaluatorEvaluations {
        evaluator: any;
        criteriumEvaluations: CriteriumEvaluation[];
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

        getCriteriumEvaluations(evaluator: any): CriteriumEvaluation[] {
            if (!evaluator) { return []; }
            const evaluatorEvaluation = this.evaluatorEvaluations.find(evaluatorEvaluation => evaluatorEvaluation.evaluator.userId === evaluator.userId);
            if (!evaluatorEvaluation) { throw new Error(`No evaluation data found for evaluator: ${evaluator.name}`); }
            return evaluatorEvaluation.criteriumEvaluations;
        }

        selectLevel(criterium: Criterium, level: Level) {
            if (!this.evaluator) { return; }
            const evaluations = (store.rubricResults.evaluations as any)[this.evaluator.userId];
            const evaluation = evaluations.find((evaluation: any) => evaluation.criteriumId === criterium.id);
            if (!evaluation) {
                evaluations.push({ criteriumId: criterium.id, levelId: level.id, feedback: ''});
            } else {
                evaluation.levelId = level.id;
            }
        }

        updateCriteriumFeedback(criterium: Criterium, feedback: string) {
            if (!this.evaluator) { return; }
            const evaluations = (store.rubricResults.evaluations as any)[this.evaluator.userId];
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
            this.evaluator = this.store.uiState.entry.options.evaluator;
            this.evaluatorEvaluations = evaluators.map((evaluator: any) => {
                const evaluations = this.store.rubricResults.evaluations[evaluator.userId];
                const criteriumEvaluations: CriteriumEvaluation[] = rubricDefaultEvaluation.map(defaultCriteriumEvaluation => {
                    const storeEvaluation = evaluations.find((evaluation: any) => evaluation.criteriumId === defaultCriteriumEvaluation.criterium.id);
                    if (storeEvaluation) {
                        const level = rubric.levels.find(level => level.id === storeEvaluation.levelId);
                        return { criterium: defaultCriteriumEvaluation.criterium, level: level!, score: rubric.getChoiceScore(defaultCriteriumEvaluation.criterium, level!), feedback: storeEvaluation.feedback };
                    } else {
                        return { ...defaultCriteriumEvaluation };
                    }
                });
                return {evaluator, criteriumEvaluations};
            });
        }

        created() {
            this.initData();
        }
    }
</script>
