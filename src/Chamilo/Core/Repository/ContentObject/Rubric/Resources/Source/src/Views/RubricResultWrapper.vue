<template>
  <div class="container-fluid">
      <rubric-result v-if="rubric" :rubric="rubric" :evaluators="evaluators" :criterium-results="criteriumResults"></rubric-result>
  </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric, {RubricJsonObject} from '../Domain/Rubric';
    import RubricResult from './RubricResult.vue';
    import {convertRubricData} from '../Util/util';
    import {CriteriumEvaluation, CriteriumResult} from '../Util/interfaces';

    @Component({
        components: {
            RubricResult
        }
    })
    export default class RubricResultWrapper extends Vue {
        private rubric: Rubric | undefined;
        private criteriumResults: CriteriumResult[] = [];
        private evaluators: any[] = [];

        @Prop({type: Object, required: true}) readonly rubricData!: object;
        @Prop({type: Array, required: true}) readonly rubricResults!: any[];

        initData() {
            const convertedRubricData = convertRubricData(this.rubricData);
            const rubric = Rubric.fromJSON(convertedRubricData as RubricJsonObject);
            const evaluators = this.rubricResults.map((res : any) =>
                ({ userId: res.user.id, name: res.user.name, role: res.user.role, targetUserId: res['target_user'].id, targetName: res['target_user'].name })
            );
            const r_evaluations = this.rubricResults.map((res : any) => res.results);

            this.criteriumResults = rubric.getAllCriteria().map(criterium => {
                const defaultEvaluation: CriteriumEvaluation = { criterium, level: null, score: 0, feedback: '' };
                const evaluations = evaluators.map((evaluator : any, index: number) => {
                    const criteriumEvaluation: CriteriumEvaluation = {...defaultEvaluation};
                    const evaluations = r_evaluations[index];
                    const criteriumEvaluationInput = evaluations.find((o: any) => String(o['tree_node_id']) === criterium.id);
                    if (criteriumEvaluationInput) {
                        const chosenLevel = rubric.levels.find(level => level.id === String(criteriumEvaluationInput['level_id']));
                        criteriumEvaluation.level = chosenLevel || null;
                        criteriumEvaluation.score = criteriumEvaluationInput.score;
                        criteriumEvaluation.feedback = criteriumEvaluationInput.comment;
                    }
                    return { evaluator, criteriumEvaluation};
                });
                return { criterium, evaluations };
            });
            this.evaluators = evaluators;
            this.rubric = rubric;
        }

        created() {
            this.initData();
        }
    }
</script>