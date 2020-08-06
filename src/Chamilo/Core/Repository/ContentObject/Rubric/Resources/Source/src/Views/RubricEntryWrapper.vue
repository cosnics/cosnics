<template>
    <div class="container-fluid">
        <rubric-entry v-if="rubric" :rubric="rubric" :criterium-evaluations="criteriumEvaluations" :ui-state="uiState" :show-errors="showErrors"
                      @level-selected="updateRubricResults" @criterium-feedback-changed="updateRubricResults">
        </rubric-entry>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric, {RubricJsonObject} from '../Domain/Rubric';
    import RubricEntry from './RubricEntry.vue';
    import {convertRubricData} from '../Util/util';
    import {CriteriumEvaluation} from '../Util/interfaces';

    @Component({
        components: {
            RubricEntry
        },
    })
    export default class RubricEntryWrapper extends Vue {
        private rubric: Rubric | undefined;
        private uiState = {
            showDefaultFeedbackFields: false
        };

        private criteriumEvaluations: CriteriumEvaluation[] = [];

        @Prop({type: Object, required: true}) readonly rubricData!: object;
        @Prop({type: Object, default: null}) readonly rubricResults!: any|null;
        @Prop({type: Boolean, default: false}) readonly showErrors!: boolean;

        updateRubricResults() {
          if (this.rubricResults !== null) {
            this.rubricResults.results = this.criteriumEvaluations.map(evaluation => (
                { 'criterium_tree_node_id': parseInt(evaluation.criterium.id),
                  'level_id': (evaluation.level !== null) ? parseInt(evaluation.level.id) : null,
                  'comment': evaluation.feedback.length > 0 ? evaluation.feedback : null
                }));
          }
        }

        initData() {
          const convertedRubricData = convertRubricData(this.rubricData);
          const rubric = this.rubric = Rubric.fromJSON(convertedRubricData as RubricJsonObject);
          const defaultLevel = rubric.levels.find(level => level.isDefault) || null;
          this.criteriumEvaluations = rubric.getAllCriteria().map(criterium =>
              ({ criterium, level: defaultLevel, score: defaultLevel ? rubric.getChoiceScore(criterium, defaultLevel) : 0, feedback: '' })
          );
          this.updateRubricResults();
        }

        created() {
          this.initData();
        }
    }
</script>