<template>
    <div id="app" v-if="rubric">
        <rubric-entry :rubric="rubric" :rubric-evaluation="rubricEvaluation" :existing-result="existingResult" :ui-state="uiState" :show-errors="showErrors"
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
    import Level from "../Domain/Level";

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
        @Prop({type: Object, default: null}) readonly existingResult!: any|null;

        updateRubricResults() {
            if (this.rubricResults !== null) {
                this.rubricResults.results = this.treeNodeEvaluations.map(evaluation => {
                    const node = evaluation.treeNode;
                    const d : any = {
                        'tree_node_id': parseInt(node.id),
                        'level_id': (evaluation.level !== null) ? parseInt(evaluation.level.id) : null,
                        'comment': evaluation.feedback.length > 0 ? evaluation.feedback : null,
                        'type': node.getType()
                    };
                    if (this.rubric!.useScores && node.getType() === 'criterium' && evaluation.level?.useRangeScore) {
                        d.score = evaluation.score;
                        if (d.score < evaluation.level?.minimumScore! || d.score > evaluation.level?.score ) {
                            d.error = 'range';
                        }
                    }
                    return d;
                });
            }
        }

        getCriteriumDefaultLevel(rubric: Rubric, criterium: Criterium, defaultLevel: Level|null) {
            const criteriumLevels = rubric.filterLevelsByCriterium(criterium);
            if (!criteriumLevels.length) { return defaultLevel; }
            return criteriumLevels.find(level => level.isDefault) || null;
        }

        getCriteriumDefaultScore(rubric: Rubric, criterium: Criterium, defaultLevel: Level|null) {
            if (!defaultLevel) { return null; }
            return defaultLevel.criteriumId ? defaultLevel.score : rubric.getChoiceScore(criterium, defaultLevel);
        }

        initData() {
            const convertedRubricData = convertRubricData(this.rubricData);
            const rubric = this.rubric = Rubric.fromJSON(convertedRubricData as RubricJsonObject);
            const defaultLevel = rubric.rubricLevels.find(level => level.isDefault) || null;
            this.treeNodeEvaluations = rubric.getAllTreeNodes().map(treeNode => {
                if (treeNode instanceof Criterium) {
                    const level = this.getCriteriumDefaultLevel(rubric, treeNode, defaultLevel);
                    const score = this.getCriteriumDefaultScore(rubric, treeNode, level);
                    return { treeNode, level, score, feedback: '' };
                }
                return { treeNode, level: null, score: null, feedback: ''};
            });
            this.rubricEvaluation = RubricEvaluation.fromEntry(rubric, this.treeNodeEvaluations);
            this.updateRubricResults();
        }

        created() {
            this.initData();
        }
    }
</script>