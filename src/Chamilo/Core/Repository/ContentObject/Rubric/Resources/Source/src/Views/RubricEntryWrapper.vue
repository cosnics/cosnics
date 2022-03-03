<template>
    <div id="app" v-if="rubric">
        <rubric-entry :rubric="rubric" :rubric-evaluation="rubricEvaluation" :ui-state="uiState" :show-errors="showErrors"
                      @level-selected="updateRubricResults" @criterium-feedback-changed="updateRubricResults">
            <template v-slot:slot-inner>
                <slot name="slot-outer"></slot>
            </template>
        </rubric-entry>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric, {RubricJsonObject} from '../Domain/Rubric';
    import Criterium from '../Domain/Criterium';
    import RubricEntry from './RubricEntry.vue';
    import {convertRubricData} from '../Util/util';
    import {TreeNodeEvaluation} from '../Util/interfaces';
    import RubricEvaluation from '../Domain/RubricEvaluation';

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

        private treeNodeEvaluations: TreeNodeEvaluation[] = [];
        private rubricEvaluation: RubricEvaluation|null = null;

        @Prop({type: Object, required: true}) readonly rubricData!: object;
        @Prop({type: Object, default: null}) readonly rubricResults!: any|null;
        @Prop({type: Boolean, default: false}) readonly showErrors!: boolean;

        updateRubricResults() {
            if (this.rubricResults !== null) {
                this.rubricResults.results = this.treeNodeEvaluations.map(evaluation => ({
                    'tree_node_id': parseInt(evaluation.treeNode.id),
                    'level_id': (evaluation.level !== null) ? parseInt(evaluation.level.id) : null,
                    'comment': evaluation.feedback.length > 0 ? evaluation.feedback : null,
                    'type': evaluation.treeNode.getType()
                }));
            }
        }

        initData() {
            const convertedRubricData = convertRubricData(this.rubricData);
            const rubric = this.rubric = Rubric.fromJSON(convertedRubricData as RubricJsonObject);
            const defaultLevel = rubric.levels.find(level => level.isDefault) || null;
            this.treeNodeEvaluations = rubric.getAllTreeNodes().map(treeNode =>
                ({ treeNode,
                   level: treeNode instanceof Criterium ? defaultLevel : null,
                   score: treeNode instanceof Criterium ? (defaultLevel ? rubric.getChoiceScore(treeNode, defaultLevel) : 0) : null,
                   feedback: '' }));
            this.rubricEvaluation = RubricEvaluation.fromEntry(rubric, this.treeNodeEvaluations);
            this.updateRubricResults();
        }

        created() {
            this.initData();
        }
    }
</script>