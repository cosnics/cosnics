<template>
    <div class="treenode-rubric-results" :class="{'rb-col-start-3': useScores && rubric.useRelativeWeights && showScores, 'is-selected': options.selectedTreeNode === treeNode, 'is-highlighted': options.highlightedTreeNode === treeNode}" @click.stop="options.selectedTreeNode = treeNode" @mouseover="options.highlightedTreeNode = treeNode" @mouseout="options.highlightedTreeNode = null">
        <tree-node-evaluation-display v-for="evaluation in evaluations"
                                      :rubric="rubric" :tree-node="treeNode" :show-scores="showScores" :score="getScore(evaluation)" :feedback="evaluation.feedback" :title-overlay="getEvaluationTitleOverlay(evaluation)" :level="evaluation.level || null"></tree-node-evaluation-display>
        <tree-node-evaluation-display v-if="useAbsoluteScores" show-max :rubric="rubric" :tree-node="treeNode" :score="maxScore" :class="{'is-selected': isCriterium && options.selectedTreeNode === treeNode}" />
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import TreeNodeEvaluationDisplay from './TreeNodeEvaluationDisplay.vue';
    import Rubric from '../../Domain/Rubric';
    import Cluster from '../../Domain/Cluster';
    import Category from '../../Domain/Category';
    import Criterium from '../../Domain/Criterium';
    import RubricEvaluation from '../../Domain/RubricEvaluation';
    import {TreeNodeEvaluation} from '../../Util/interfaces';

    @Component({
        components: {TreeNodeEvaluationDisplay}
    })
    export default class TreeNodeRubricResults extends Vue {

        @Prop({type: Rubric}) readonly rubric!: Rubric;
        @Prop({type: [Rubric, Cluster, Category, Criterium]}) readonly treeNode!: Rubric|Cluster|Category|Criterium;
        @Prop({type: Number, default: null}) readonly maxScore!: number|null;
        @Prop({type: Object, default: () => ({})}) readonly options!: any;
        @Prop({type: Array, default: () => []}) readonly evaluations!: any[];
        @Prop({type: RubricEvaluation, required: true}) readonly rubricEvaluation!: RubricEvaluation;
        @Prop({type: Boolean, default: true}) readonly showScores!: boolean;

        get nodeType() {
            return this.treeNode.getType();
        }

        get isCriterium() {
            return this.nodeType === 'criterium';
        }

        get useScores() {
            return this.rubric.useScores;
        }

        get useAbsoluteScores() {
            return this.rubric.useScores && !this.rubric.useRelativeWeights;
        }

        get useGrades() {
            return !this.rubric.useScores;
        }

        getEvaluationTitleOverlay(evaluation: TreeNodeEvaluation) : string {
            const extraFeedback = evaluation.feedback ? `${this.$t('extra-feedback')}: ${evaluation.feedback}` : '';
            if (this.nodeType === 'criterium') {
                return this.rubric.useScores ? extraFeedback : `${evaluation.level?.title || ''}${extraFeedback && ('\n' + extraFeedback)}`;
            } else {
                return extraFeedback;
            }
        }

        getScore(evaluation: TreeNodeEvaluation): number|null {
            switch (this.nodeType) {
                case 'cluster':
                    return this.rubricEvaluation.getClusterScore(this.treeNode as Cluster, evaluation);
                case 'category':
                    return this.rubricEvaluation.getCategoryScore(this.treeNode as Category, evaluation);
                case 'criterium':
                    return evaluation.score;
                case 'rubric':
                    return this.rubricEvaluation.getRubricScore(evaluation);
                default:
                    return null;
            }
        }
    }
</script>

<style lang="scss" scoped>
    .treenode-rubric-results {
        align-items: center;
        cursor: default;
        display: flex;
        position: relative;
        z-index: 0;

        &.is-highlighted {
            &::before, &::after {
                background: hsla(230, 15%, 97%, 1);
            }
        }

        &.is-selected {
            &::before, &::after {
                background: hsla(230, 15%, 94%, 1);
            }

            &::after {
                border-right: .5rem solid hsla(215, 45%, 55%, 1);
            }
        }

        &::before, &::after {
            bottom: -.5rem;
            content: '';
            position: absolute;
            top: -.5rem;
        }

        &::before {
            right: -.7rem;
            left: 0;
            z-index: -1;
        }

        &::after {
            right: -3rem;
            width: 3rem;
        }
    }
</style>

<style lang="scss">
    .treenode-evaluation {
        &:not(:last-child) {
            margin-right: .7rem;
        }

        &.mod-criterium-max.is-selected {
            border: 1px solid #dae1ec;
            background: #e1e7ef;
            padding: .1rem .6rem;
        }
    }
</style>