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
        <div v-if="rubric" class="rubric-results-view" @click="selectedCriterium = null">
            <div class="rubric mod-res">
                <div class="rubric-header-fill" aria-hidden="true"></div>
                <ul class="rubric-header">
                    <li class="rubric-header-title mod-res" v-for="evaluator in evaluators" :class="{ 'mod-grades': !rubric.useScores }" :title="evaluator.name">{{ evaluator.name|capitalize }}</li>
                    <li v-if="rubric.useScores" class="rubric-header-title mod-res mod-max">Max.</li>
                </ul>
                <h1 class="rubric-title">{{ rubric.title }}</h1>
                <template v-for="{cluster, maxScore, evaluations} in getClusterRowsData(rubric)">
                    <div class="treenode-title-header-wrap">
                        <div class="treenode-title-header mod-res">
                            <div class="treenode-title-header-pre"></div>
                            <h1 class="treenode-title cluster-title">{{ cluster.title }}</h1>
                        </div>
                    </div>
                    <ul class="evaluations">
                        <li v-for="(evaluation, index) in evaluations" class="score-result-view" :class="{ 'mod-empty': !rubric.useScores && !evaluation.feedback }">
                            <div class="score-number-calc mod-result-view mod-cluster" :id="`${cluster.id}-evaluation-${index}`" :class="{ 'mod-grades': !rubric.useScores }" :title="`${ evaluation.feedback ? $t('extra-feedback') + ': ' + evaluation.feedback : ''}`">
                                <i v-if="evaluation.feedback" class="score-feedback-icon fa fa-info mod-cluster" />
                                {{ rubric.useScores ? getClusterScore(cluster, evaluation) : '' }}
                            </div>
                        </li>
                        <li v-if="rubric.useScores" class="score-result-view">
                            <div class="score-number-calc mod-result-view mod-cluster-max">{{ maxScore }}</div>
                        </li>
                    </ul>
                    <template v-for="{category, maxScore, evaluations} in getCategoryRowsData(cluster)">
                        <div class="treenode-title-header-wrap">
                            <div class="treenode-title-header mod-res" :style="`--category-color: ${ category.title && category.color ? category.color : 'transparent' }`">
                                <div class="treenode-title-header-pre mod-category"></div>
                                <h2 class="treenode-title category-title">{{ category.title }}</h2>
                            </div>
                        </div>
                        <ul class="evaluations">
                            <li v-for="(evaluation, index) in evaluations" class="score-result-view" :class="{ 'mod-empty': !rubric.useScores && !evaluation.feedback }">
                                <div class="score-number-calc mod-result-view mod-category" :id="`${category.id}-evaluation-${index}`" :class="{ 'mod-grades': !rubric.useScores }" :title="`${ evaluation.feedback ? $t('extra-feedback') + ': ' + evaluation.feedback : ''}`">
                                    <i v-if="evaluation.feedback" class="score-feedback-icon fa fa-info mod-category" />
                                    {{ rubric.useScores ? getCategoryScore(category, evaluation) : '' }}
                                </div>
                            </li>
                            <li v-if="rubric.useScores" class="score-result-view">
                                <div class="score-number-calc mod-result-view mod-category-max">{{ maxScore }}</div>
                            </li>
                        </ul>
                        <template v-for="{criterium, maxScore, evaluations} in getCriteriumRowsData(category)">
                            <div class="treenode-title-header-wrap" @click.stop="selectedCriterium = criterium">
                                <div class="treenode-title-header mod-res" :style="`--category-color: ${ !(category.title && category.color) ? '#999' : category.color }`">
                                    <div class="treenode-title-header-pre mod-criterium"></div>
                                    <h3 class="treenode-title criterium-title">{{ criterium.title }}</h3>
                                </div>
                            </div>
                            <ul class="evaluations">
                                <li v-for="(evaluation, index) in evaluations" class="subtotal criterium-total mod-result-view" :class="{'mod-grades': !rubric.useScores }" :title="`${ !rubric.useScores ? evaluation.level.title : ''}${ evaluation.feedback ? ( !rubric.useScores ? '\n' : '') + $t('extra-feedback') + ': ' + evaluation.feedback : ''}`">
                                    <div class="mod-result-view" :class="rubric.useScores ? 'score-number-calc mod-criterium' : 'graded-level'" :id="`${criterium.id}-evaluation-${index}`">
                                        <i v-if="evaluation.feedback" class="score-feedback-icon fa fa-info"/>
                                        {{ rubric.useScores ? evaluation.score : evaluation.level.title }}
                                    </div>
                                </li>
                                <li v-if="rubric.useScores" class="subtotal criterium-total mod-result-view">
                                    <div class="score-number-calc mod-result-view mod-criterium-max">{{ maxScore }}</div>
                                </li>
                            </ul>
                        </template>
                    </template>
                </template>
                <template v-if="rubric.useScores">
                    <div class="total-title mod-res">{{ $t('total') }} {{ $t('rubric') }}:</div>
                    <ul class="evaluations">
                        <li v-for="evaluator in evaluators" class="score-result-view">
                            <div class="score-number-calc mod-result-view mod-rubric">{{ getRubricScore(evaluator) }}</div>
                        </li>
                        <li class="score-result-view">
                            <div class="score-number-calc mod-result-view mod-rubric-max">{{ rubric.getMaximumScore() }}</div>
                        </li>
                    </ul>
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

        @Prop({type: Rubric}) readonly rubric!: Rubric;
        @Prop({type: Array, default: () => []}) readonly evaluators!: any[];
        @Prop({type: Array, default: () => []}) readonly treeNodeResults!: TreeNodeResult[];
        @Prop({type: Object, default: () => ({})}) readonly options!: any;

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
    .rubric {
        display: grid;
        grid-column-gap: .7rem;
        grid-row-gap: .7rem;
        max-width: max-content;
        padding: 1rem;
        position: relative;

        &.mod-res {
            grid-template-columns: minmax(20rem, 30rem) minmax(calc(var(--num-cols) * 6rem), calc(var(--num-cols) * 12rem));
        }
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
    .rubric-title {
        display: none;
    }
    .treenode-header {
        grid-column-start: 1;
    }
    .treenode-title-header-wrap {
        align-items: center;
        display: flex;
        grid-column-start: 1;
        position: relative;
    }

    .treenode-title-header.mod-res {
        flex: 1;
    }

    .total-title.mod-res {
        grid-column-start: 1;
    }
</style>

<style lang="scss">
    .table-header-filler.mod-result-view {
        display: block;
        margin-right: 1rem;
        max-width: 25rem;
        min-width: 25rem;
    }

    .evaluations {
        display: flex;
        flex: 1;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .cluster-header.mod-result-view, .category-header.mod-result-view {
        margin-right: 1rem;
    }

    .clusters.mod-result-view {
        margin-top: .75em;
    }

    .cluster-title.mod-result-view {
        flex: 1;
        margin-left: .2em;
    }

    .category-title.mod-result-view {
        flex: 1;
    }

    .cluster-row.mod-result-view {
        display: flex;
        align-items: center;
    }

    .category-row.mod-result-view {
        display: flex;
        align-items: center;
    }

    .rubric-results-view {
        /*max-width: 60em;*/
        position: relative;
        /*width: 60em;*/
    }

    .evaluator-table-header-date {
        color:hsla(200, 30%, 40%, 1);
        font-size:1.2rem;
        margin: 0 1em 0 .5em;
        text-align: right;

        &.mod-grades {
            text-align: left;
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

        &.criterium-header, &.cluster-header, &.category-header {
            flex: 1;
            max-width: 25rem;
            min-width: 25rem;
        }

        &.criterium-header {
            margin-right: .5rem;
        }

        /*&.subtotal {
            margin-right: .5em;
        }*/

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

            &.mod-cluster, &.mod-cluster-max {
                margin-bottom: .5em;
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

    .btn-info-close {
        align-items: center;
        background-color: $bg-criterium-details;
        border: 1px solid transparent;
        border-radius: $border-radius;
        color: #777;
        display: flex;
        float: right;
        height: 1.6em;
        justify-content: center;
        margin-left: .5em;
        margin-top: .3em;
        padding: 0;
        transition: background-color 200ms, color 200ms;
        width: 1.6em;

        &:hover {
            background-color: $btn-color;
            border: 1px solid transparent;
            border-radius: $border-radius;
            color: #fff;
        }

        &:focus {
            border: 1px solid $input-color-focus;
        }
    }

    .rr-selected-criterium-wrapper{
        margin-top: 1em;
    }

    .rr-selected-criterium {
        max-width: 80ch;
    }


    .choice-feedback {
        line-height: 1.5em;
        /*white-space: pre-line;*/

        ul {
            list-style: disc;
        }

        ul, ol {
            margin: 0 0 0 2rem;
            padding: 0;
        }
    }

    @media only screen and (min-width: 900px) {
        .btn-info-close {
            display: none;
        }

        .rr-selected-criterium-wrapper {
            border-left: 1px solid hsla(191, 21%, 80%, 1);
            margin-left: 1.5em;
            padding-left: 1.5em;
            width: 40%;
            pointer-events: none;
        }

        .rr-selected-criterium {
            position: sticky;
            top: 10px;
        }
    }

    @media only screen and (max-width: 899px) {
        .rr-selected-criterium-wrapper {
            align-items: flex-start;
            background: hsla(0, 0, 0, .15);
            display: flex;
            height: 100%;
            justify-content: center;
            left: 0;
            margin-top: 0;
            overflow: auto;
            padding-top: 3em;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 10000;
        }

        .rr-selected-criterium {
            background: #fff;
            border-radius: $border-radius;
            box-shadow: 1px 1px 5px #999;
            margin: 0 1em;
            padding: .5em;
        }
    }

    .rr-selected-criterium-results {
        /*background: #e4e3e3;*/
        border-radius: $border-radius;
        padding: .5em;

    }

    .rr-selected-criterium-results-title {
        color: hsla(191, 41%, 38%, 1);
        font-size: 1.4rem;
        font-weight: 700;
        line-height: 1.3em;
        margin-bottom: .5em;
        max-width: 75ch;

        .separator {
            margin: 0 .3em;
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
    }
    /*.rubric.mod-result-view {
        display: flex;
    }*/
</style>
