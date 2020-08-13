<template>
    <div class="container-fluid">
        <rubric-result v-if="rubric" :rubric="rubric" :evaluators="store.rubricResults.evaluators" :criterium-results="criteriumResults"></rubric-result>
    </div>
</template>

<script lang="ts">
    import {Component, Vue} from 'vue-property-decorator';
    import Rubric, {RubricJsonObject} from '../Domain/Rubric';
    import RubricResult from './RubricResult.vue';
    import store from '../store';
    import {CriteriumEvaluation, CriteriumResult} from '../Util/interfaces';

    @Component({
        components: {
            RubricResult
        },
    })
    export default class RubricResultDemoWrapper extends Vue {
        private rubric: Rubric | undefined;
        private store: any = store;
        private criteriumResults: CriteriumResult[] = [];

        initData() {
            const rubric = Rubric.fromJSON(this.store.rubricData as RubricJsonObject);
            const results = this.store.rubricResults;
            const evaluators = results.evaluators;

            rubric.getAllCriteria().forEach(criterium => {
                const criteriumResult: CriteriumResult = { criterium: criterium, evaluations: {} };
                evaluators!.forEach((evaluator : string) => {
                    const criteriumEvaluation: CriteriumEvaluation = { criterium, level: null, score: 0, feedback: '' };
                    const evaluations = results.evaluations[evaluator];
                    const criteriumEvaluationInput = evaluations.find((o: any) => o.criteriumId === criterium.id);
                    if (criteriumEvaluationInput) {
                        const chosenLevel = rubric.levels.find(level => level.id === criteriumEvaluationInput.levelId);
                        if (chosenLevel) {
                            criteriumEvaluation.level = chosenLevel;
                            criteriumEvaluation.score = rubric.getChoiceScore(criterium, chosenLevel);
                            criteriumEvaluation.feedback = criteriumEvaluationInput.feedback;
                        }
                    }
                    criteriumResult.evaluations[evaluator] = criteriumEvaluation;
                });
                this.criteriumResults.push(criteriumResult);
              });
              this.rubric = rubric;
        }

        created() {
            this.initData();
        }
    }
</script>
