<i18n>
{
    "en": {
        "chose": "chose",
        "extra-feedback": "Extra feedback",
        "gave-score": "gave a score of",
        "level-descriptions": "Level descriptions",
        "rubric": "Rubric",
        "total": "Total"
    },
    "fr": {
        "chose": "a choisi",
        "extra-feedback": "Feed-back supplémentaire",
        "gave-score": "a donné le score",
        "level-descriptions": "Descriptions de niveau",
        "rubric": "Rubrique",
        "total": "Total"
    },
    "nl": {
        "chose": "koos",
        "extra-feedback": "Extra feedback",
        "gave-score": "gaf score",
        "level-descriptions": "Niveauomschrijvingen",
        "rubric": "Rubric",
        "total": "Totaal"
    }
}
</i18n>

<template>
    <div id="app" :class="{ 'mod-sep': this.options.isDemo }">
        <div v-if="rubric" class="rubric mod-result-view">
            <link rel="stylesheet"
                  href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
            <div class="rubric-results-view" @click="selectedCriterium = null">
                <div class="rubric-table-header">
                    <div class="evaluators-table-header">
                        <div v-for="evaluator in evaluators" class="evaluator-table-header-title" :class="{ 'mod-grades': !rubric.useScores }" :title="evaluator.name">{{ evaluator.name|capitalize }}</div>
                        <div v-if="rubric.useScores" class="evaluator-table-header-title mod-max">Max.</div>
                    </div>
                </div>
                <h1 class="rubric-title">{{ rubric.title }}</h1>
                <ul class="clusters mod-result-view">
                    <li v-for="cluster in rubric.clusters" class="cluster-list-item" v-if="rubric.getAllCriteria(cluster).length > 0">
                        <div class="cluster">
                            <div class="cluster-row mod-result-view">
                                <h2 class="cluster-title" style="flex:1">{{ cluster.title }}</h2>
                                <div v-for="(evaluator, index) in evaluators" class="score-result-view" :class="{ 'mod-empty': !rubric.useScores && !getTreeNodeEvaluation(cluster, evaluator).feedback }">
                                    <div class="score-number-calc mod-result-view mod-cluster" :id="`${cluster.id}-evaluation-${index}`" :class="{ 'mod-grades': !rubric.useScores }">
                                        <i v-if="getTreeNodeEvaluation(cluster, evaluator).feedback" class="score-feedback-icon fa fa-info mod-cluster" />
                                        {{ rubric.useScores ? getClusterScore(cluster, evaluator) : '' }}
                                    </div>
                                    <b-tooltip v-if="getTreeNodeEvaluation(cluster, evaluator).feedback" triggers="hover focus" :target="`${cluster.id}-evaluation-${index}`" placement="bottom">
                                        {{ getTreeNodeEvaluation(cluster, evaluator).feedback }}
                                    </b-tooltip>
                                </div>
                                <div v-if="rubric.useScores" class="score-result-view">
                                    <div class="score-number-calc mod-result-view mod-cluster-max">{{ getClusterMaxScore(cluster) }}</div>
                                </div>
                            </div>
                            <ul class="categories">
                                <li v-for="category in cluster.categories" class="category-list-item" :style="`--category-color: ${ category.title && category.color ? category.color : 'transparent' }`"  v-if="rubric.getAllCriteria(category).length > 0">
                                    <div class="category">
                                        <div v-if="category.title" class="category-row mod-result-view">
                                            <h3 class="category-title category-indicator" style="flex:1">{{ category.title }}</h3>
                                            <div v-for="(evaluator, index) in evaluators" class="score-result-view" :class="{ 'mod-empty': !rubric.useScores && !getTreeNodeEvaluation(category, evaluator).feedback }">
                                                <div class="score-number-calc mod-result-view mod-category" :id="`${category.id}-evaluation-${index}`" :class="{ 'mod-grades': !rubric.useScores }">
                                                    <i v-if="getTreeNodeEvaluation(category, evaluator).feedback" class="score-feedback-icon fa fa-info mod-category" />
                                                    {{ rubric.useScores ? getCategoryScore(category, evaluator) : '' }}
                                                </div>
                                                <b-tooltip v-if="getTreeNodeEvaluation(category, evaluator).feedback" triggers="hover focus" :target="`${category.id}-evaluation-${index}`" placement="bottom">
                                                    {{ getTreeNodeEvaluation(category, evaluator).feedback }}
                                                </b-tooltip>
                                            </div>
                                            <div v-if="rubric.useScores" class="score-result-view">
                                                <div class="score-number-calc mod-result-view mod-category-max">{{ getCategoryMaxScore(category) }}</div>
                                            </div>
                                        </div>
                                        <ul class="criteria" :style="`--category-color: ${ !(category.title && category.color) ? '#999' : '' }`">
                                            <li v-for="criterium in category.criteria" class="criterium-list-item mod-result-view" :class="{'is-selected': selectedCriterium === criterium}" @click.stop="selectedCriterium = criterium">
                                                <div class="criterium mod-result-view">
                                                    <div class="criterium-title-header mod-result-view">
                                                        <h4 class="criterium-title category-indicator">{{ criterium.title }}</h4>
                                                    </div>
                                                    <div v-for="(evaluator, index) in evaluators" class="subtotal criterium-total mod-result-view" :class="{'mod-grades': !rubric.useScores }" :title="rubric.useScores ? '' : getTreeNodeEvaluation(criterium, evaluator).level.title">
                                                        <div class="mod-result-view" :class="rubric.useScores ? 'score-number-calc mod-criterium' : 'graded-level'" :id="`${criterium.id}-evaluation-${index}`">
                                                            <i v-if="getTreeNodeEvaluation(criterium, evaluator).feedback" class="score-feedback-icon fa fa-info"/>
                                                            {{ rubric.useScores ? getCriteriumScore(criterium, evaluator) : getTreeNodeEvaluation(criterium, evaluator).level.title }}
                                                        </div>
                                                        <b-tooltip v-if="getTreeNodeEvaluation(criterium, evaluator).feedback" triggers="hover focus" :target="`${criterium.id}-evaluation-${index}`" placement="bottom">
                                                            {{ getTreeNodeEvaluation(criterium, evaluator).feedback }}
                                                        </b-tooltip>
                                                    </div>
                                                    <div v-if="rubric.useScores" class="subtotal criterium-total mod-result-view">
                                                        <div class="score-number-calc mod-result-view mod-criterium-max">{{ getCriteriumMaxScore(criterium) }}</div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                            <!--<div v-if="rubric.useScores" class="subtotal cluster-total mod-result-view">
                                <div class="cluster-total-title">{{ $t('total') }} {{ cluster.title }}:</div>
                                <div v-for="evaluator in evaluators" class="score-result-view">
                                    <div class="score-number-calc mod-result-view mod-cluster">{{ getClusterScore(cluster, evaluator) }}</div>
                                </div>
                                <div class="score-result-view">
                                    <div class="score-number-calc mod-result-view mod-cluster-max">{{ getClusterMaxScore(cluster) }}</div>
                                </div>
                            </div>-->
                        </div>
                    </li>
                </ul>
                <div v-if="rubric.useScores" class="subtotal rubric-total mod-result-view">
                    <div class="rubric-total-title mod-result-view">{{ $t('total') }} {{ $t('rubric') }}:</div>
                    <div v-for="evaluator in evaluators" class="score-result-view">
                        <div class="score-number-calc mod-result-view mod-rubric">{{ getRubricScore(evaluator) }}</div>
                    </div>
                    <div class="score-result-view">
                        <div class="score-number-calc mod-result-view mod-rubric-max">{{ rubric.getMaximumScore() }}</div>
                    </div>
                </div>
            </div>
            <div v-if="selectedCriterium" class="rr-selected-criterium" @click.stop="">
                <div style="position:sticky;top:10px;">
                <div class="rr-selected-criterium-results">
                    <div class="title" style="font-size: 1.4rem">
                        <!--<span>{{ selectedCriterium.parent.parent.title }}<i class="fa fa-angle-right separator" /></span>
                        <span v-if="selectedCriterium.parent.title.trim().length !== 0">{{ selectedCriterium.parent.title }}<i class="fa fa-angle-right separator" /></span>
                        --><span>{{ selectedCriterium.title }}</span>
                    </div>
                    <div class="rr-selected-result" v-for="evaluator in evaluators">
                        <p v-if="rubric.useScores && getTreeNodeEvaluation(selectedCriterium, evaluator).level !== null"><span>{{ evaluator.name|capitalize }}</span> {{ $t('gave-score') }} <span>{{ getCriteriumScore(selectedCriterium, evaluator) }}</span> (<span class="score-title">{{
                                getTreeNodeEvaluation(selectedCriterium, evaluator).level.title
                            }}</span>)</p>
                        <p v-else-if="getTreeNodeEvaluation(selectedCriterium, evaluator).level !== null"><span>{{ evaluator.name|capitalize }}</span> {{ $t('chose') }}
                            '<span class="score-title">{{
                                    getTreeNodeEvaluation(selectedCriterium, evaluator).level.title
                                }}</span>'</p>
                        <p v-if="getTreeNodeEvaluation(selectedCriterium, evaluator).feedback">
                            {{ $t('extra-feedback') }}: {{
                                getTreeNodeEvaluation(selectedCriterium, evaluator).feedback
                            }}
                        </p>
                    </div>
                </div>
                <div class="rr-selected-criterium-levels">
                    <div class="title">{{ $t('level-descriptions') }}:</div>
                    <ul class="levels-list">
                        <li v-for="level in rubric.levels" :key="level.id" class="levels-list-item">
                            <div class="levels-list-item-header">
                                <div class="title">{{ level.title }}</div>
                                <div class="choice-score" v-if="rubric.useScores">{{ rubric.getChoiceScore(selectedCriterium, level) }}</div>
                            </div>
                            <div class="choice-feedback">
                                {{ rubric.getChoice(selectedCriterium, level).feedback }}
                            </div>
                        </li>
                    </ul>
                </div>
                </div>
            </div>
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
    import {TreeNodeEvaluation, TreeNodeResult, EvaluatorEvaluation} from '../Util/interfaces';

    function add(v1: number, v2: number) {
        return v1 + v2;
    }

    @Component({
        filters: {
            capitalize: function (value: any) {
                if (!value) { return ''; }
                value = value.toString();
                return value.charAt(0).toUpperCase() + value.slice(1);
            }
        }
    })
    export default class RubricResult extends Vue {
        private selectedCriterium: Criterium|null = null;

        @Prop({type: Rubric}) readonly rubric!: Rubric;
        @Prop({type: Array, default: () => []}) readonly evaluators!: any[];
        @Prop({type: Array, default: () => []}) readonly treeNodeResults!: TreeNodeResult[];
        @Prop({type: Object, default: () => ({})}) readonly options!: any;

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

        getCategoryScore(category: Category, evaluator: any) : number {
            if (!this.rubric) { return 0; }
            return this.rubric.getAllCriteria(category).map(criterium => this.getCriteriumScore(criterium, evaluator)).reduce(add, 0);
        }

        getClusterScore(cluster: Cluster, evaluator: any) : number {
            if (!this.rubric) { return 0; }
            return this.rubric.getAllCriteria(cluster).map(criterium => this.getCriteriumScore(criterium, evaluator)).reduce(add, 0);
        }

        getRubricScore(evaluator: any) : number {
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
    .clusters.mod-result-view {
        margin-top: .75em;
    }

    .cluster-row.mod-result-view {
        display: flex;
        align-items: center;
    }

    .category-row.mod-result-view {
        display: flex;
        align-items: center;
    }
    .rubric-total-title.mod-result-view {
        margin-right: .5em;
    }
    .rubric-results-view {
        /*max-width: 60em;*/
        position: relative;
        width: 60em;
    }
    .rubric-total.subtotal.mod-result-view {
        margin-right: 0;
    }

    .evaluators-table-header {
        justify-content: flex-end;
    }

    .evaluator-table-header-title {
        flex: initial;
        width: 8.33em;

        /*&.mod-grades {
            width: 6.95em;
        }*/

        &.mod-max {
            background: hsla(203, 33%, 60%, 1);
            color: #fff;
        }
    }

    .mod-result-view {
        /*.rubric {
            // Why did i put this here? Setting it removes the sticky
            overflow-x: auto;
        }*/

        &.criterium-list-item {
            border: 1px solid transparent;
            border-radius: $border-radius;

            &.is-selected {
                /*background: hsla(224, 20%, 68%, 0.3);*/
                /*.score-number {
                    background: none;
                    border-bottom: 1px solid darken($score-lighter, 20%);
                }*/
            }

            &:hover {
                /*background: hsla(224, 20%, 68%, 0.4);
                border: 1px solid darken($score-lighter, 20%);
                */cursor: pointer;

                .score-number {
                    /*background: none;*/
                    /*border-bottom: 1px solid darken($score-lighter, 20%);*/
                    cursor: pointer;
                }
            }
        }

        &.criterium {
            align-items: center;
        }

        &.criterium-title-header {
            flex: 1;
        }

        &.subtotal {
            margin-right: .5em;
        }

        &.criterium-total {
            cursor: default;
            margin-right: .5em;
            width: 9em;

            /*&.mod-grades {
                width: 7.5em;
            }*/
        }

        &.score-number-calc {
            font-size: 1.6rem;
            line-height: 1.4em;
            padding-top: .1em;

            &.mod-category {
                background: $score-light;
            }

            &.mod-criterium {
                background: $score-lighter;
            }

            &.mod-category.mod-grades, &.mod-cluster.mod-grades {
                text-align: left;
                padding-left: .25em;
            }
        }

        &.graded-level {
            background: $score-lighter;
            border: 1px solid transparent;
            border-radius: $border-radius;
            color: #666;
            font-size: 1.2rem;
            line-height: 2.3em;
            overflow: hidden;
            padding: .133em .2em 0 .45em;
            text-overflow: ellipsis;
            white-space: nowrap;
            width: 100%;
        }
    }

    .score-feedback-icon {
        color: #2787ad;
        font-size: 1.1rem;
        margin-right: .1em;

        &.fa-info {
            font-size: 1.6rem;
        }

        &.mod-cluster {
            color: hsla(0, 0%, 100%, .85);
        }
    }

    .score-result-view {
        margin-right: .5em;
        width: 9.0em;

        &.mod-empty {
            opacity: 0;
        }
    }

    .rr-selected-criterium {
        margin-top: 1em;
        max-width: 80ch;
    }

    .choice-feedback {
        line-height: 1.5em;
        white-space: pre-line;
    }

    @media only screen and (min-width: 900px) {
        .rr-selected-criterium {
            border-left: 1px solid hsla(191, 21%, 80%, 1);
            margin-left: 1.5em;
            padding-left: 1.5em;
            width: 40%;
        }
    }

    .rr-selected-criterium-results {
        /*background: #e4e3e3;*/
        border-radius: $border-radius;
        padding: .5em;

        .title {
            color: hsla(191, 41%, 38%, 1);
            font-weight: 700;
            line-height: 1.3em;
            margin-bottom: .5em;

            .separator {
                margin: 0 .3em;
            }
        }
    }

    .rr-selected-result {
        border-radius: $border-radius;
        margin-bottom: 1em;

        p {
            margin: 0;
            white-space: pre-line;
        }

        span {
            font-weight: bold;

            &.score-title {
                color: hsla(191, 41%, 33%, 1);
            }
        }
    }

    .rr-selected-criterium-levels {
        /*background: #e4e3e3;*/
        margin-top: 1.5em;
        padding: .5em;

        .title {
            font-size: 1.4rem;
            font-weight: bold;
            margin-bottom: 0;
            margin-top: 0;
        }

        > .title {
            color: hsla(191, 41%, 38%, 1);
        }

        .levels-list {
            list-style: none;
            margin-top: 0;
            padding: 0;
        }

        .levels-list-item {
            margin-bottom: .75em;
        }

        .levels-list-item-header {
            align-items: baseline;
            border-bottom: 1px solid #d8dddf;
            display: flex;
            width: 100%;

            .title {
                flex: 1;
                font-weight: 700;
            }

            .choice-score {
                font-size: 2rem;
                text-align: right;
            }

            .choice-feedback {
                margin: .25em 1.5em 1.25em 0;
            }
        }
    }

    @media only screen and (min-width: 900px) {
        .rubric.mod-result-view {
            display: flex;
        }
    }
</style>
