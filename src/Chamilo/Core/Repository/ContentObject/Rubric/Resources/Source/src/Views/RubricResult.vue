<i18n>
{
    "en": {
        "extra-feedback": "Extra feedback",
        "rubric": "Rubric",
        "total": "Total"
    },
    "fr": {
        "extra-feedback": "Feed-back supplémentaire",
        "rubric": "Rubrique",
        "total": "Total"
    },
    "nl": {
        "extra-feedback": "Extra feedback",
        "rubric": "Rubric",
        "total": "Totaal"
    }
}
</i18n>

<template>
    <div id="app" :class="{ 'mod-sep': this.options.isDemo }">
        <link rel="stylesheet"
              href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <div v-if="rubric" class="rubric-results-view">
            <div class="rubric mod-res" :style="{'--num-cols': evaluators.length + (useScores ? 1 : 0)}" @click.stop="selectedCriterium = null">
                <!--<div class="rubric-header-fill" aria-hidden="true"></div>-->
                <ul class="rubric-header mod-res">
                    <li class="rubric-header-title mod-res" v-for="evaluator in evaluators"
                        :class="{ 'mod-grades': useGrades }" :title="evaluator.name">{{ evaluator.name|capitalize }}</li>
                    <li v-if="useScores" class="rubric-header-title mod-res mod-max">Max.</li>
                </ul>
                <ul class="rubric-header mod-res mod-date">
                    <li class="rubric-header-date" v-for="evaluator in evaluators"
                        :class="{ 'mod-grades': useGrades }" :title="evaluator.name">{{ new Date(evaluator.date)|formatDate }}</li>
                    <li v-if="useScores" class="rubric-header-date mod-max" aria-hidden="true"></li>
                </ul>
                <template v-for="{cluster, maxScore, evaluations} in getClusterRowsData(rubric)">
                    <div class="treenode-title-header-wrap">
                        <div class="treenode-title-header mod-res">
                            <div class="treenode-title-header-pre"></div>
                            <h1 class="treenode-title cluster-title">{{ cluster.title }}</h1>
                        </div>
                    </div>
                    <div class="treenode-rubric-results">
                        <div class="treenode-evaluations">
                            <div class="treenode-evaluation mod-cluster" :class="{'mod-grades': useGrades, 'mod-hide': useGrades && !evaluation.feedback}" v-for="evaluation in evaluations">
                                <i v-if="evaluation.feedback" class="treenode-feedback-icon mod-cluster fa fa-info" :title="getEvaluationTitleOverlay(evaluation)" />
                                {{ useScores ? getClusterScore(cluster, evaluation) : '' }}
                            </div>
                            <div class="treenode-evaluation mod-cluster-max" v-if="useScores">{{ maxScore }}</div>
                        </div>
                    </div>
                    <template v-for="{category, maxScore, evaluations} in getCategoryRowsData(cluster)">
                        <template v-if="category.title && rubric.getAllCriteria(category).length > 0">
                            <div class="treenode-title-header-wrap">
                                <div class="treenode-title-header mod-res" :style="`--category-color: ${ category.title && category.color ? category.color : 'transparent' }`">
                                    <div class="treenode-title-header-pre mod-category"></div>
                                    <h2 class="treenode-title category-title">{{ category.title }}</h2>
                                </div>
                            </div>
                            <div class="treenode-rubric-results">
                                <div class="treenode-evaluations">
                                    <div class="treenode-evaluation mod-category" :class="{'mod-grades': useGrades, 'mod-hide': useGrades && !evaluation.feedback}" v-for="evaluation in evaluations">
                                        <i v-if="evaluation.feedback" class="treenode-feedback-icon fa fa-info" :title="getEvaluationTitleOverlay(evaluation)" />
                                        {{ useScores ? getCategoryScore(category, evaluation) : '' }}
                                    </div>
                                    <div class="treenode-evaluation mod-category-max" v-if="useScores">{{ maxScore }}</div>
                                </div>
                            </div>
                        </template>
                        <template v-for="{criterium, maxScore, evaluations} in getCriteriumRowsData(category)">
                            <div class="treenode-title-header-wrap" :class="{'is-selected': selectedCriterium === criterium, 'is-highlighted': highlightedCriterium === criterium}" @click.stop="selectedCriterium = criterium" @mouseover="highlightedCriterium = criterium" @mouseout="highlightedCriterium = null">
                                <div class="treenode-title-header mod-res" :style="`--category-color: ${ !(category.title && category.color) ? '#999' : category.color }`">
                                    <div class="treenode-title-header-pre mod-criterium"></div>
                                    <h3 class="treenode-title criterium-title u-markdown-criterium" v-html="criterium.toMarkdown()"></h3>
                                </div>
                            </div>
                            <div class="treenode-rubric-results" @click.stop="selectedCriterium = criterium" @mouseover="highlightedCriterium = criterium" @mouseout="highlightedCriterium = null">
                                <div class="treenode-evaluations">
                                    <div class="treenode-evaluation mod-criterium" :class="{'mod-grades': useGrades, 'is-selected': selectedCriterium === criterium, 'is-highlighted': highlightedCriterium === criterium}" v-for="evaluation in evaluations">
                                        <i v-if="evaluation.feedback" class="treenode-feedback-icon fa fa-info" :title="getEvaluationTitleOverlay(evaluation)" />
                                        {{ useScores ? evaluation.score : evaluation.level.title }}
                                    </div>
                                    <div class="treenode-evaluation mod-criterium-max" :class="{'is-selected': selectedCriterium === criterium, 'is-highlighted': highlightedCriterium === criterium}" v-if="useScores">{{ maxScore }}</div>
                                </div>
                            </div>
                        </template>
                    </template>
                    <div class="cluster-sep" :class="{ 'mod-grades': useGrades }"></div>
                </template>
                <template v-if="useScores">
                    <div class="total-title mod-res">{{ $t('total') }} {{ $t('rubric') }}:</div>
                    <div class="treenode-rubric-results">
                        <div class="treenode-evaluations">
                            <div class="treenode-evaluation mod-rubric" v-for="evaluator in evaluators">{{ getRubricScore(evaluator) }}</div>
                            <div class="treenode-evaluation mod-rubric-max">{{ rubric.getMaximumScore() }}</div>
                        </div>
                    </div>
                </template>
            </div>
            <criterium-results-view v-if="selectedCriterium" :rubric="rubric" :criterium="selectedCriterium" :evaluations="getCriteriumRowData(selectedCriterium).evaluations" @close="selectedCriterium = null"></criterium-results-view>
        </div>
    </div>
</template>
<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric from '../Domain/Rubric';
    import TreeNode from '../Domain/TreeNode';
    import Cluster from '../Domain/Cluster';
    import Category from '../Domain/Category';
    import Criterium from '../Domain/Criterium';
    import CriteriumResultsView from '../Components/CriteriumResultsView.vue';
    import {TreeNodeEvaluation, TreeNodeResult, EvaluatorEvaluation} from '../Util/interfaces';

    function add(v1: number, v2: number) {
        return v1 + v2;
    }

    function pad(num: number) : string {
        return `${num < 10 ? '0' : ''}${num}`;
    }

    @Component({
        components: { CriteriumResultsView },
        filters: {
            capitalize: function (value: any) {
                if (!value) { return ''; }
                value = value.toString();
                return value.charAt(0).toUpperCase() + value.slice(1);
            },
            formatDate: function (date: Date) {
                if (isNaN(date.getDate())) { // todo: dates with timezone offsets, e.g. +0200 result in NaN data in Safari. For now, return an empty string.
                    return '';
                }
                return `${pad(date.getDate())}/${pad(date.getMonth() + 1)}/${date.getFullYear()} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
            }
        }
    })
    export default class RubricResult extends Vue {
        private selectedCriterium: Criterium|null = null;
        private highlightedCriterium: Criterium|null = null;

        @Prop({type: Rubric}) readonly rubric!: Rubric;
        @Prop({type: Array, default: () => []}) readonly evaluators!: any[];
        @Prop({type: Array, default: () => []}) readonly treeNodeResults!: TreeNodeResult[];
        @Prop({type: Object, default: () => ({})}) readonly options!: any;

        getEvaluationTitleOverlay(evaluation: TreeNodeEvaluation) : string {
            const extraFeedback = evaluation.feedback ? `${this.$t('extra-feedback')}: ${evaluation.feedback}` : '';
            if (evaluation.treeNode.getType() === 'criterium') {
                return this.rubric.useScores ? extraFeedback : `${evaluation.level?.title || ''}${extraFeedback && ('\n' + extraFeedback)}`;
            } else {
                return extraFeedback;
            }
        }

        get useScores() {
            return this.rubric.useScores;
        }

        get useGrades() {
            return !this.rubric.useScores;
        }

        getClusterRowsData(rubric: Rubric) {
            return rubric.clusters
                .filter(cluster => cluster.hasChildren())
                .map(cluster => ({
                    cluster,
                    maxScore: this.getClusterMaxScore(cluster),
                    evaluations: this.getTreeNodeResult(cluster).evaluations.map(_ => ({evaluator: _.evaluator, ..._.treeNodeEvaluation}))
                }));
        }

        getCategoryRowsData(cluster: Cluster) {
            return cluster.categories
                .filter(category => category.hasChildren())
                .map(category => ({
                    category,
                    maxScore: this.getCategoryMaxScore(category),
                    evaluations: this.getTreeNodeResult(category).evaluations.map(_ => ({evaluator: _.evaluator, ..._.treeNodeEvaluation}))
                }));
        }

        getCriteriumRowsData(category: Category) {
            return category.criteria.map(criterium => this.getCriteriumRowData(criterium));
        }

        getCriteriumRowData(criterium: Criterium) {
            return {
                criterium,
                maxScore: this.getCriteriumMaxScore(criterium),
                evaluations: this.getTreeNodeResult(criterium).evaluations.map(_ => ({evaluator: _.evaluator, ..._.treeNodeEvaluation}))
            };
        }

        getCriteriumMaxScore(criterium: Criterium) : number {
            const scores : number[] = [0];
            const iterator = this.rubric!.choices.get(criterium.id);
            iterator!.forEach((choice, levelId) => {
                const level = this.rubric!.levels.find(level => level.id === levelId);
                scores.push(this.rubric!.getChoiceScore(criterium, level!));
            })
            return Math.max.apply(null, scores);
        }

        getCategoryMaxScore(category: Category) : number {
            if (!this.rubric) { return 0; }
            return this.rubric.getAllCriteria(category).map(criterium => this.getCriteriumMaxScore(criterium)).reduce(add, 0);
        }

        getClusterMaxScore(cluster: Cluster) : number {
            if (!this.rubric) { return 0; }
            return this.rubric.getAllCriteria(cluster).map(criterium => this.getCriteriumMaxScore(criterium)).reduce(add, 0);
        }

        getCriteriumScore(criterium: Criterium, evaluator: any) : number {
            return this.getTreeNodeEvaluation(criterium, evaluator).score || 0;
        }

        getCategoryScore(category: Category, evaluation: any) : number {
            if (typeof evaluation?.score === 'number') { return evaluation.score; }
            if (!this.rubric) { return 0; }
            return this.rubric.getAllCriteria(category).map(criterium => this.getCriteriumScore(criterium, evaluation?.evaluator)).reduce(add, 0);
        }

        getClusterScore(cluster: Cluster, evaluation: any) : number {
            if (typeof evaluation?.score === 'number') { return evaluation.score; }
            if (!this.rubric) { return 0; }
            return this.rubric.getAllCriteria(cluster).map(criterium => this.getCriteriumScore(criterium, evaluation?.evaluator)).reduce(add, 0);
        }

        getRubricScore(evaluator: any) : number {
            const treeNodeScore = this.getTreeNodeEvaluation(this.rubric, evaluator).score;
            if (typeof treeNodeScore === 'number') { return treeNodeScore; }
            if (!this.rubric) { return 0; }
            return this.rubric.getAllCriteria().map(criterium => this.getCriteriumScore(criterium, evaluator)).reduce(add, 0);
        }

        getTreeNodeResult(treeNode: TreeNode) : TreeNodeResult {
            const treeNodeResult = this.treeNodeResults.find((_ : TreeNodeResult) => _.treeNode === treeNode);
            if (!treeNodeResult) { throw new Error(`No data found for: ${treeNode}`); }
            return treeNodeResult;
        }

        getTreeNodeEvaluation(treeNode: TreeNode, evaluator: any) : TreeNodeEvaluation {
            const evaluatorEvaluation = this.getTreeNodeResult(treeNode).evaluations.find((_ : EvaluatorEvaluation) => _.evaluator === evaluator);
            if (!evaluatorEvaluation) { throw new Error(`No evaluation found for: ${treeNode} and evaluator: ${evaluator && evaluator.name}`); }
            return evaluatorEvaluation.treeNodeEvaluation;
        }
    }
</script>
<style lang="scss">
    .rubric-results-view {
        display: flex;
    }

    .rubric.mod-res {
        align-self: flex-start;
        grid-template-columns: minmax(20rem, 30rem) minmax(calc(var(--num-cols) * 6rem), calc(var(--num-cols) * 12rem));
    }

    .rubric-header.mod-res {
        grid-column-start: 2;
    }

    .rubric-header.mod-date {
        margin-top: -1.5rem;
        z-index: 29;
    }

    .rubric-header-title.mod-res {
        text-align: right;

        &.mod-grades {
            text-align: left;
        }

        &.mod-max {
            background: hsla(203, 33%, 60%, 1);
        }
    }

    .rubric-header-date {
        color: hsla(200, 30%, 40%, 1);
        flex: 1;
        font-size: 1.2rem;
        padding: 0 .5rem;
        text-align: right;

        &.mod-max {
            visibility: hidden;
        }

        &:not(:last-child) {
            margin-right: .7rem;
        }

        &.mod-grades {
            text-align: left;
        }
    }

    /* Todo */
    .evaluator-table-header-date {
        color: hsla(200, 30%, 40%, 1);
        font-size: 1.2rem;
        margin: 0 1em 0 .5em;
        text-align: right;

        &.mod-grades {
            text-align: left;
        }
    }

    .treenode-title-header-wrap {
        align-items: center;
        display: flex;
        grid-column-start: 1;
        position: relative;

        &::before, & + .treenode-rubric-results::before {
            bottom: -.5rem;
            content: '';
            position: absolute;
            right: -.7rem;
            top: -.5rem;
        }

        &::before {
            border-left: .5rem solid transparent;
            left: -1rem;
            transition: 200ms border;
        }

        & + .treenode-rubric-results::before {
            left: 0;
            z-index: 10;
        }

        &.is-highlighted {
            &::before, & + .treenode-rubric-results::before {
                background: hsla(130, 6%, 91%, 1);
                background: hsla(230, 15%, 91%, 1);
            }

            &::before {
                border-color: hsla(215, 45%, 60%, 1);
            }
        }

        &.is-selected {
            &::before, & + .treenode-rubric-results::before {
                background: hsla(235, 25%, 88%, 1);
            }

            &::before {
                border-color: hsla(215, 45%, 55%, 1);
            }
        }
    }

    .treenode-title-header.mod-res {
        flex: 1;
    }

    .treenode-rubric-results {
        /*align-self: center;*/
        align-items: center;
        display: flex;
        position: relative;
        z-index: 10;
    }

    .treenode-evaluations {
        display: flex;
        width: 100%;
        z-index: 20;
    }

    .treenode-evaluation {
        border-radius: $border-radius;
        color: #666;
        flex: 1;
        font-size: 1.6rem;
        padding: .2rem .7rem;
        text-align: right;

        &:not(:last-child) {
            margin-right: .7rem;
        }

        &.is-selected {
            box-shadow: 0 1px 2px hsla(236, 25%, 80%, 1);
        }

        &.is-highlighted {
            box-shadow: 0 1px 2px hsla(190, 15%, 80%, 1);
        }

        &.mod-grades {
            font-size: 1.2rem;
            overflow: hidden;
            text-align: left;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        &.mod-rubric {
            background: $score-darker;
            color: #fff;
        }

        &.mod-cluster {
            background: $score-dark;
            color: #fff;
        }

        &.mod-category {
            background: $score-light;
        }

        &.mod-criterium {
            background: $score-lighter;
        }

        &.mod-rubric-max {
            background: hsla(207, 40%, 35%, 1);
            color: #fff;
        }

        &.mod-cluster-max {
            background: hsla(203, 33%, 60%, 1);
            color: #fff;
        }

        &.mod-category-max {
            background: hsla(203, 32%, 83%, 1);
        }

        &.mod-criterium-max {
            background: hsla(213, 30%, 93%, 1);
        }

        &.mod-hide {
            background: none;
        }
    }

    .total-title.mod-res {
        grid-column-start: 1;
    }
</style>