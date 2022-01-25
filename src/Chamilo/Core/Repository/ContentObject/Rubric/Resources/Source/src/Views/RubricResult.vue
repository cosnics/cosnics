<i18n>
{
    "en": {
        "extra-feedback": "Extra feedback",
        "rubric": "Rubric",
        "total": "Total",
        "weight": "Weight"
    },
    "fr": {
        "extra-feedback": "Feed-back supplémentaire",
        "rubric": "Rubrique",
        "total": "Total",
        "weight": "Poids"
    },
    "nl": {
        "extra-feedback": "Extra feedback",
        "rubric": "Rubric",
        "total": "Totaal",
        "weight": "Gewicht"
    }
}
</i18n>

<template>
    <div id="app" :class="{ 'mod-sep': this.options.isDemo }">
        <link rel="stylesheet"
              href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <div v-if="rubric" class="rubric-results-view">
            <div class="rubric" :class="useScores && rubric.useRelativeWeights && showScores ? 'mod-res-w' : 'mod-res'" :style="{'--num-cols': evaluators.length + (useAbsoluteScores ? 1 : 0)}" @click.stop="selectedTreeNode = null">
                <ul class="rubric-tools" v-if="rubric.useScores && rubric.useRelativeWeights">
                    <li class="app-tool-item"><button class="btn-check" :class="{ checked: showScores }" @click.stop="showScores = !showScores"><span class="lbl-check" tabindex="-1"><i class="btn-icon-check fa" aria-hidden="true" />Cijferweergave</span></button></li>
                </ul>
                <div v-if="rubric.useScores && rubric.useRelativeWeights && showScores" class="treenode-weight-header rb-col-start-2">
                    <div style="flex: 1; text-align: center; padding: 0.7rem; font-weight: 600;">{{ $t('weight') }}</div>
                </div>
                <ul class="rubric-header" :class="useScores && rubric.useRelativeWeights && showScores ? 'rb-col-start-3' : 'rb-col-start-2'" v-if="useScores || (useGrades && evaluators.length)">
                    <li class="rubric-header-title mod-res" v-for="evaluator in evaluators"
                        :class="{ 'mod-grades': useGrades }" :title="evaluator.name">{{ evaluator.name|capitalize }}</li>
                    <li v-if="useAbsoluteScores" class="rubric-header-title mod-res mod-max">Max.</li>
                </ul>
                <ul class="rubric-header mod-date" :class="useScores && rubric.useRelativeWeights && showScores ? 'rb-col-start-3' : 'rb-col-start-2'" v-if="useScores || (useGrades && evaluators.length)">
                    <li class="rubric-header-date" v-for="evaluator in evaluators"
                        :class="{ 'mod-grades': useGrades }" :title="evaluator.name">{{ evaluator.date|formatDate }}</li>
                    <li v-if="useAbsoluteScores" class="rubric-header-date mod-max" aria-hidden="true"></li>
                </ul>
                <template v-for="{cluster, maxScore, evaluations} in getClusterRowsData(rubric)">
                    <div class="treenode-title-header-wrap rb-col-start-1" :class="{'is-selected': selectedTreeNode === cluster, 'is-highlighted': highlightedTreeNode === cluster}" @click.stop="selectedTreeNode = cluster" @mouseover="highlightedTreeNode = cluster" @mouseout="highlightedTreeNode = null">
                        <div class="treenode-title-header mod-res">
                            <!--<div class="treenode-title-header-pre"></div>-->
                            <h1 class="treenode-title cluster-title">{{ cluster.title }}</h1>
                        </div>
                    </div>
                    <div class="treenode-weight mod-res-w" :class="{'is-selected': selectedTreeNode === cluster, 'is-highlighted': highlightedTreeNode === cluster}" v-if="useScores && rubric.useRelativeWeights && showScores" @click.stop="selectedTreeNode = cluster" @mouseover="highlightedTreeNode = cluster" @mouseout="highlightedTreeNode = null">
                        <span>{{ rubric.getRelativeWeight(cluster)|formatNum }}</span><span class="sr-only">%</span><i class="fa fa-percent" aria-hidden="true"></i>
                    </div>
                    <div class="treenode-rubric-results" :class="{'rb-col-start-3': useScores && rubric.useRelativeWeights && showScores, 'is-selected': selectedTreeNode === cluster, 'is-highlighted': highlightedTreeNode === cluster}" @click.stop="selectedTreeNode = cluster" @mouseover="highlightedTreeNode = cluster" @mouseout="highlightedTreeNode = null">
                        <div class="treenode-evaluations">
                            <div class="treenode-evaluation mod-cluster" :class="{'mod-grades': useGrades || (useScores && rubric.useRelativeWeights && !showScores), 'is-selected': selectedTreeNode === cluster, 'is-highlighted': highlightedTreeNode === cluster}" v-for="evaluation in evaluations">
                                <i v-if="evaluation.feedback" class="treenode-feedback-icon fa fa-info" :class="{'mod-cluster': !(useGrades || (useScores && rubric.useRelativeWeights && !showScores)) }" :title="getEvaluationTitleOverlay(evaluation)" />
                                <score-display v-if="useScores && (!rubric.useRelativeWeights || showScores)" :score="rubricEvaluation.getClusterScore(cluster, evaluation)" :percent="rubric.useRelativeWeights" />
                            </div>
                            <div v-if="useAbsoluteScores" class="treenode-evaluation mod-cluster-max">
                                <score-display :score="maxScore" />
                            </div>
                        </div>
                    </div>
                    <template v-for="({category, maxScore, evaluations}, index) in getCategoryRowsData(cluster)">
                        <template v-if="category.title && rubric.getAllCriteria(category).length > 0">
                            <div class="treenode-title-header-wrap mod-category" :class="{'is-selected': selectedTreeNode === category, 'is-highlighted': highlightedTreeNode === category, 'has-category': !!category.title}" @click.stop="selectedTreeNode = category" @mouseover="highlightedTreeNode = category" @mouseout="highlightedTreeNode = null" :style="`--category-color: ${ category.title && category.color ? category.color : '#999' }`">
                                <div class="treenode-title-header mod-res">
                                    <div class="treenode-title-header-pre mod-category" :class="{'mod-no-color': !category.color}"></div>
                                    <h2 class="treenode-title category-title">{{ category.title }}</h2>
                                </div>
                            </div>
                            <div class="treenode-weight mod-res-w" :class="{'is-selected': selectedTreeNode === category, 'is-highlighted': highlightedTreeNode === category}" v-if="useScores && rubric.useRelativeWeights && showScores" @click.stop="selectedTreeNode = category" @mouseover="highlightedTreeNode = category" @mouseout="highlightedTreeNode = null">
                                <span>{{ rubric.getRelativeWeight(category)|formatNum }}</span><span class="sr-only">%</span><i class="fa fa-percent" aria-hidden="true"></i>
                            </div>
                            <div class="treenode-rubric-results" :class="{'rb-col-start-3': useScores && rubric.useRelativeWeights && showScores, 'is-selected': selectedTreeNode === category, 'is-highlighted': highlightedTreeNode === category}" @click.stop="selectedTreeNode = category" @mouseover="highlightedTreeNode = category" @mouseout="highlightedTreeNode = null">
                                <div class="treenode-evaluations">
                                    <div class="treenode-evaluation mod-category" :class="{'mod-grades': useGrades || (useScores && rubric.useRelativeWeights && !showScores), 'is-selected': selectedTreeNode === category, 'is-highlighted': highlightedTreeNode === category}" v-for="evaluation in evaluations">
                                        <i v-if="evaluation.feedback" class="treenode-feedback-icon fa fa-info" :title="getEvaluationTitleOverlay(evaluation)" />
                                        <score-display v-if="useScores && (!rubric.useRelativeWeights || showScores)" :score="rubricEvaluation.getCategoryScore(category, evaluation)" :percent="rubric.useRelativeWeights" />
                                    </div>
                                    <div v-if="useAbsoluteScores" class="treenode-evaluation mod-category-max">
                                        <score-display :score="maxScore" />
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-for="{criterium, maxScore, evaluations} in getCriteriumRowsData(category)">
                            <div class="treenode-title-header-wrap" :class="{'is-selected': selectedTreeNode === criterium, 'is-highlighted': highlightedTreeNode === criterium, 'has-category':  category.title }" @click.stop="selectedTreeNode = criterium" @mouseover="highlightedTreeNode = criterium" @mouseout="highlightedTreeNode = null" :style="`--category-color: ${ !(category.title && category.color) ? '#999' : category.color }`">
                                <div class="treenode-title-header mod-res">
                                    <div class="treenode-title-header-pre mod-criterium"></div>
                                    <h3 class="treenode-title criterium-title u-markdown-criterium" :class="{'mod-no-category': !category.title}" v-html="criterium.toMarkdown()"></h3>
                                </div>
                            </div>
                            <div class="treenode-weight mod-res-w" :class="{'is-selected': selectedTreeNode === criterium, 'is-highlighted': highlightedTreeNode === criterium}" v-if="useScores && rubric.useRelativeWeights && showScores" @click.stop="selectedTreeNode = criterium" @mouseover="highlightedTreeNode = criterium" @mouseout="highlightedTreeNode = null">
                                <span>{{ rubric.getCriteriumWeight(criterium)|formatNum }}</span><span class="sr-only">%</span><i class="fa fa-percent" aria-hidden="true"></i>
                            </div>
                            <div class="treenode-rubric-results" :class="{'rb-col-start-3': useScores && rubric.useRelativeWeights && showScores, 'is-selected': selectedTreeNode === criterium, 'is-highlighted': highlightedTreeNode === criterium}" @click.stop="selectedTreeNode = criterium" @mouseover="highlightedTreeNode = criterium" @mouseout="highlightedTreeNode = null">
                                <div class="treenode-evaluations">
                                    <div class="treenode-evaluation mod-criterium" :class="{'mod-grades': useGrades || (useScores && rubric.useRelativeWeights && !showScores), 'is-selected': selectedTreeNode === criterium, 'is-highlighted': highlightedTreeNode === criterium}" v-for="evaluation in evaluations">
                                        <i v-if="evaluation.feedback" class="treenode-feedback-icon fa fa-info" :title="getEvaluationTitleOverlay(evaluation)" />
                                        <score-display v-if="useScores && (!rubric.useRelativeWeights || showScores)" :score="evaluation.score" :percent="rubric.useRelativeWeights" />
                                        <template v-else>{{ evaluation.level.title }}</template>
                                    </div>
                                    <div v-if="useAbsoluteScores" class="treenode-evaluation mod-criterium-max" :class="{'is-selected': selectedTreeNode === criterium, 'is-highlighted': highlightedTreeNode === criterium}">
                                        <score-display :score="maxScore" />
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div class="category-sep" v-if="index < cluster.categories.length - 1" style="height: .75rem"></div>
                    </template>
                    <div class="cluster-sep" :class="{ 'mod-grades': useGrades || (useScores && rubric.useRelativeWeights && !showScores) }"></div>
                </template>
                <template v-if="useScores && (!rubric.useRelativeWeights || showScores)">
                    <div class="total-title" :class="{'mod-res-col': useScores && rubric.useRelativeWeights && showScores}">{{ $t('total') }} {{ $t('rubric') }}:</div>
                    <div class="treenode-rubric-results" :class="{'rb-col-start-3': useScores && rubric.useRelativeWeights && showScores}">
                        <div class="treenode-evaluations">
                            <div class="treenode-evaluation mod-rubric" v-for="evaluator in evaluators">
                                <score-display :score="rubricEvaluation.getRubricScore(evaluator)" :percent="rubric.useRelativeWeights" />
                            </div>
                            <div class="treenode-evaluation mod-rubric-max" v-if="!rubric.useRelativeWeights">
                                <score-display :score="rubric.getMaximumScore()" />
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <tree-node-results-view v-if="selectedTreeNode" :rubric="rubric" :tree-node="selectedTreeNode" :evaluations="getTreeNodeRowData(selectedTreeNode).evaluations" @close="selectedTreeNode = null"></tree-node-results-view>
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
    import RubricEvaluation from '../Domain/RubricEvaluation';
    import TreeNodeResultsView from '../Components/TreeNodeResultsView.vue';
    import ScoreDisplay from '../Components/ScoreDisplay.vue';
    import {TreeNodeEvaluation} from '../Util/interfaces';

    function pad(num: number) : string {
        return `${num < 10 ? '0' : ''}${num}`;
    }

    @Component({
        components: { TreeNodeResultsView, ScoreDisplay },
        filters: {
            capitalize: function (value: any) {
                if (!value) { return ''; }
                value = value.toString();
                return value.charAt(0).toUpperCase() + value.slice(1);
            },
            formatDate: function (s: string) {
                const date = new Date(s);
                if (isNaN(date.getDate())) { // todo: dates with timezone offsets, e.g. +0200 result in NaN data in Safari. For now, return an empty string.
                    return '';
                }
                return `${pad(date.getDate())}/${pad(date.getMonth() + 1)}/${date.getFullYear().toString().substr(-2)} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
            },
            formatNum: function (v: number) {
                return v.toLocaleString(undefined, {maximumFractionDigits: 2});
            }
        }
    })
    export default class RubricResult extends Vue {
        private selectedTreeNode: Criterium|Category|Cluster|null = null;
        private highlightedTreeNode: Criterium|Category|Cluster|null = null;
        private showScores = false;

        @Prop({type: Rubric}) readonly rubric!: Rubric;
        @Prop({type: RubricEvaluation, required: true}) readonly rubricEvaluation!: RubricEvaluation;
        @Prop({type: Object, default: () => ({})}) readonly options!: any;

        get useScores() {
            return this.rubric.useScores;
        }

        get useAbsoluteScores() {
            return this.rubric.useScores && !this.rubric.useRelativeWeights;
        }

        get useGrades() {
            return !this.rubric.useScores;
        }

        get evaluators() {
            return this.rubricEvaluation.getEvaluators();
        }

        getEvaluationTitleOverlay(evaluation: TreeNodeEvaluation) : string {
            const extraFeedback = evaluation.feedback ? `${this.$t('extra-feedback')}: ${evaluation.feedback}` : '';
            if (evaluation.treeNode.getType() === 'criterium') {
                return this.rubric.useScores ? extraFeedback : `${evaluation.level?.title || ''}${extraFeedback && ('\n' + extraFeedback)}`;
            } else {
                return extraFeedback;
            }
        }

        getTreeNodeRowData(treeNode: TreeNode) {
            if (treeNode instanceof Criterium) {
                return this.getCriteriumRowData(treeNode);
            }
            if (treeNode instanceof Category) {
                return this.getCategoryRowData(treeNode);
            }
            if (treeNode instanceof Cluster) {
                return this.getClusterRowData(treeNode);
            }
            return { maxScore: 0, evaluations: [] };
        }

        getClusterRowsData(rubric: Rubric) {
            return rubric.clusters
                .filter(cluster => cluster.hasChildren())
                .map(this.getClusterRowData);
        }

        getClusterRowData(cluster: Cluster) {
            return {
                cluster,
                maxScore: this.rubric.getClusterMaxScore(cluster),
                evaluations: this.rubricEvaluation.getEvaluations(cluster)
            };
        }

        getCategoryRowsData(cluster: Cluster) {
            return cluster.categories
                .filter(category => category.hasChildren())
                .map(this.getCategoryRowData);
        }

        getCategoryRowData(category: Category) {
            return {
                category,
                maxScore: this.rubric.getCategoryMaxScore(category),
                evaluations: this.rubricEvaluation.getEvaluations(category)
            };
        }

        getCriteriumRowsData(category: Category) {
            return category.criteria.map(criterium => this.getCriteriumRowData(criterium));
        }

        getCriteriumRowData(criterium: Criterium) {
            return {
                criterium,
                maxScore: this.rubric.getCriteriumMaxScore(criterium),
                evaluations: this.rubricEvaluation.getEvaluations(criterium)
            };
        }
    }
</script>
<style lang="scss">
    .rubric-results-view {
        display: flex;
    }

    .rubric.mod-res {
        align-self: flex-start;
        grid-template-columns: minmax(max-content, 23rem) minmax(calc(var(--num-cols) * 6rem), calc(var(--num-cols) * 12rem));
    }

    .rubric.mod-res-w {
        align-self: flex-start;
        grid-template-columns: minmax(max-content, 23rem) 7rem minmax(calc(var(--num-cols) * 6rem), calc(var(--num-cols) * 12rem));
    }

    .rubric-header.mod-date {
        margin-top: -1.5rem;
        z-index: 29;
    }

    .rubric-header-title.mod-res {
        background-color: hsl(203, 38%, 53%);
        box-shadow: none;
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

    .treenode-title-header-wrap {
        align-items: center;
        display: flex;
        min-height: 2.7rem;
        position: relative;

        &::before {
            bottom: -.5rem;
            border-left: .5rem solid transparent;
            content: '';
            left: -1rem;
            position: absolute;
            right: -.7rem;
            top: -.5rem;
            transition: 200ms border;
        }

        &.is-highlighted::before {
            background: hsla(230, 15%, 97%, 1);
        }

        &.is-selected::before {
            background: hsla(230, 15%, 94%, 1);
        }
    }

    .treenode-title-header.mod-res {
        flex: 1;
    }

    .treenode-weight.mod-res-w {
        padding-top: .25rem;
        position: relative;
        text-align: center;
        z-index: 10;

        &::before {
            bottom: -.5rem;
            content: '';
            left: 0;
            position: absolute;
            right: -.7rem;
            top: -.5rem;
            z-index: -1;
        }

        &.is-highlighted::before {
            background: hsla(230, 15%, 97%, 1);
        }

        &.is-selected::before {
            background: hsla(230, 15%, 94%, 1);
        }
    }

    .treenode-rubric-results {
        align-items: center;
        display: flex;
        position: relative;
        z-index: 10;

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
            z-index: 10;
        }

        &::after {
            right: -3rem;
            width: 3rem;
        }
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

        &.mod-grades {
            font-size: 1.2rem;
            overflow: hidden;
            text-align: left;
            text-overflow: ellipsis;
            white-space: nowrap;

            &::after {
                content: '';
                display: inline-block;
            }
        }

        &.mod-rubric {
            background: $score-darker;
            color: #fff;
        }

        &.mod-cluster.mod-grades {
            background: #fff;
            border: 1px solid hsla(190, 30%, 95%, 1);
        }

        &.mod-cluster:not(.mod-grades) {
            background: $score-dark;
            color: #fff;
        }

        &.mod-category.mod-grades {
            background: #fff;
            border: 1px solid hsla(190, 30%, 95%, 1);
        }

        &.mod-category:not(.mod-grades) {
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

        .fa-percent {
            font-size: 1rem;
            opacity: .75;
        }

        > span {
            white-space: nowrap;
        }
    }

    .total-title.mod-res-col {
        grid-column: 1 / -2;
    }
</style>

<style lang="scss" scoped>
    .treenode-title-header .treenode-title {
        max-width: 22.2rem;
    }

    .treenode-title-header-wrap.has-category::after {
        position: absolute;
        top: -0.5rem;
        width: 1px;
        bottom: -0.2rem;
        left: 0.6rem;
        background-color: var(--category-color);
        content: '';
        opacity: .5;
    }

    .treenode-title-header-wrap.mod-category::after {
        top: .8rem;
    }

    .treenode-title-header-pre.mod-criterium::after {
        border: 1px solid var(--category-color);
        content: '';
        border-radius: 50%;
        height: 7px;
        margin-top: 0.3rem;
        width: 7px;
        position: absolute;
        left: 0.3rem;
        background-color: white;
    }

    .criterium-title {
        padding-left: .75rem;
    }

    .criterium-title.mod-no-category {
        padding-left: .25rem;
    }

    .treenode-title-header-pre.mod-category::after {
        border-radius: 50%;
    }

    .treenode-title-header-pre.mod-category.mod-no-color::after {
        border: 1px solid #bbb;
        background-color: #fff;
        width: 1.1rem;
        height: 1.1rem;
        position: absolute;
        left: .1rem;
    }

    .treenode-evaluation.mod-criterium {
        background-color: #fafafa;
        border: 1px solid #deebee;
    }

    .treenode-title.cluster-title {
        margin-left: .25rem;
    }

    .treenode-feedback-icon.fa-info {
        margin-right: .5rem;
        font-size: 1.4rem;
    }

    .cluster-sep {
        border-color: #deebee;
        margin: 1rem 0 1.5rem;
    }
</style>