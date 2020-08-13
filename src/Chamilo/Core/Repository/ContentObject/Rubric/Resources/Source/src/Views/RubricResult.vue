<template>
    <div id="app" class="mod-sep" >
        <div v-if="rubric" class="rubric mod-result-view">
            <link rel="stylesheet"
                  href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
            <div class="rubric-results-view" @click="selectedCriterium = null">
                <div class="rubric-table-header">
                    <div class="evaluators-table-header">
                        <div v-for="evaluator in evaluators" class="evaluator-table-header-title">{{ evaluator|capitalize }}</div>
                        <div class="evaluator-table-header-title mod-max">Max.</div>
                    </div>
                </div>
                <h1 class="rubric-title">{{ rubric.title }}</h1>
                <ul class="clusters">
                    <li v-for="cluster in rubric.clusters" class="cluster-list-item" v-if="rubric.getAllCriteria(cluster).length > 0">
                        <div class="cluster">
                            <h2 class="cluster-title">{{ cluster.title }}</h2>
                            <ul class="categories">
                                <li v-for="category in cluster.categories" class="category-list-item" :style="`--category-color: ${ category.title ? (category.color || '#999') : '#999' }`"  v-if="rubric.getAllCriteria(category).length > 0">
                                    <div class="category">
                                        <h3 v-if="category.title" class="category-title category-indicator">{{ category.title }}</h3>
                                        <ul class="criteria">
                                            <li v-for="criterium in category.criteria" class="criterium-list-item mod-result-view" :class="{'is-selected': selectedCriterium === criterium}" @click.stop="selectedCriterium = criterium">
                                                <div class="criterium mod-result-view">
                                                    <div class="criterium-title-header mod-result-view">
                                                        <h4 class="criterium-title category-indicator">{{ criterium.title }}</h4>
                                                    </div>
                                                    <div v-for="evaluator in evaluators" class="subtotal criterium-total mod-result-view">
                                                        <div class="score-number-calc mod-result-view mod-criterium" :id="`${criterium.id}-${evaluator}`">
                                                            <i v-if="getCriteriumEvaluation(criterium, evaluator).feedback" class="icon-score-feedback fa fa-info"/>
                                                            {{ getCriteriumScore(criterium, evaluator) }}
                                                        </div>
                                                        <b-tooltip v-if="getCriteriumEvaluation(criterium, evaluator).feedback" triggers="hover focus" :target="`${criterium.id}-${evaluator}`" placement="bottom">{{
                                                            getCriteriumEvaluation(criterium, evaluator).feedback
                                                          }}</b-tooltip>
                                                    </div>
                                                    <div class="subtotal criterium-total mod-result-view">
                                                        <div class="score-number-calc mod-result-view mod-criterium-max">{{ getCriteriumMaxScore(criterium) }}</div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                            <div class="subtotal cluster-total mod-result-view">
                                <div class="cluster-total-title">Totaal {{ cluster.title }}:</div>
                                <div v-for="evaluator in evaluators" class="score-result-view">
                                    <div class="score-number-calc mod-result-view mod-cluster">{{ getClusterScore(cluster, evaluator) }}</div>
                                </div>
                                <div class="score-result-view">
                                    <div class="score-number-calc mod-result-view mod-cluster-max">{{ getClusterMaxScore(cluster) }}</div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="subtotal rubric-total mod-result-view">
                    <div class="rubric-total-title">Totaal Rubric:</div>
                    <div v-for="evaluator in evaluators" class="score-result-view">
                        <div class="score-number-calc mod-result-view mod-rubric">{{ getRubricScore(evaluator) }}</div>
                    </div>
                    <div class="score-result-view">
                        <div class="score-number-calc mod-result-view mod-rubric-max">{{ rubric.getMaximumScore() }}</div>
                    </div>
                </div>
            </div>
            <div v-if="selectedCriterium" class="rr-selected-criterium" @click.stop="">
                <div class="rr-selected-criterium-results">
                    <div class="title">
                        <span>{{ selectedCriterium.parent.parent.title }}<i class="fa fa-angle-right separator" /></span>
                        <span v-if="selectedCriterium.parent.title.trim().length !== 0">{{ selectedCriterium.parent.title }}<i class="fa fa-angle-right separator" /></span>
                        <span>{{ selectedCriterium.title }}</span>
                    </div>
                    <div class="rr-selected-result" v-for="evaluator in evaluators">
                        <p v-if="getCriteriumEvaluation(selectedCriterium, evaluator).level !== null"><span>{{ evaluator|capitalize }}</span> gaf score <span>{{ getCriteriumScore(selectedCriterium, evaluator) }}</span> (<span class="score-title">{{
                            getCriteriumEvaluation(selectedCriterium, evaluator).level.title
                          }}</span>)</p>
                        <p v-if="getCriteriumEvaluation(selectedCriterium, evaluator).feedback">
                          Extra feedback: {{
                            getCriteriumEvaluation(selectedCriterium, evaluator).feedback
                          }}
                        </p>
                    </div>
                </div>
                <div class="rr-selected-criterium-levels">
                    <div class="title">Niveaus:</div>
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
</template>
<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric, {RubricJsonObject} from '../Domain/Rubric';
    import Cluster from '../Domain/Cluster';
    import Category from '../Domain/Category';
    import Criterium from '../Domain/Criterium';
    import {CriteriumEvaluation} from '../Util/interfaces';

    interface CriteriumResult {
        criterium: Criterium,
        evaluations: any;
    }

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
        @Prop({type: Array, default: () => []}) readonly evaluators!: string[];
        @Prop({type: Array, default: () => []}) readonly criteriumResults!: CriteriumResult[];

        getCriteriumMaxScore(criterium: Criterium) : number {
            const scores : number[] = [0];
            const iterator = this.rubric!.choices.get(criterium.id);
            iterator!.forEach((choice, levelId) => {
                const level = this.rubric!.levels.find(level => level.id === levelId);
                scores.push(this.rubric!.getChoiceScore(criterium, level!));
            })
            return Math.max.apply(null, scores);
        }

        getClusterMaxScore(cluster: Cluster) : number {
            if (!this.rubric) { return 0; }
            return this.rubric.getAllCriteria(cluster).map(criterium => this.getCriteriumMaxScore(criterium)).reduce(add, 0);
        }

        getCriteriumScore(criterium: Criterium, evaluator: string) : number {
            return this.getCriteriumResult(criterium).evaluations[evaluator].score || 0;
        }

        getCategoryScore(category: Category, evaluator: string) : number {
            if (!this.rubric) { return 0; }
            return this.rubric.getAllCriteria(category).map(criterium => this.getCriteriumScore(criterium, evaluator)).reduce(add, 0);
        }

        getClusterScore(cluster: Cluster, evaluator: string) : number {
            if (!this.rubric) { return 0; }
            return this.rubric.getAllCriteria(cluster).map(criterium => this.getCriteriumScore(criterium, evaluator)).reduce(add, 0);
        }

        getRubricScore(evaluator: string) : number {
            if (!this.rubric) { return 0; }
            return this.rubric.getAllCriteria().map(criterium => this.getCriteriumScore(criterium, evaluator)).reduce(add, 0);
        }

        getCriteriumResult(criterium: Criterium) : CriteriumResult {
            const criteriumResult = this.criteriumResults.find((_ : CriteriumResult) => _.criterium === criterium);
            if (!criteriumResult) { throw new Error(`No data found for criterium: ${criterium}`); }
            return criteriumResult;
        }

        getCriteriumEvaluation(criterium: Criterium, evaluator: string) : CriteriumEvaluation {
            return this.getCriteriumResult(criterium).evaluations[evaluator];
        }
    }
</script>
<style lang="scss">
    .rubric-results-view {
        /*max-width: 60em;*/
        position: relative;
        width: 46em;
    }

    .evaluators-table-header {
        justify-content: flex-end;
    }

    .evaluator-table-header-title {
        flex: initial;
        width: 4.6em;

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
                background: hsla(224, 20%, 68%, 0.3);
                /*.score-number {
                    background: none;
                    border-bottom: 1px solid darken($score-lighter, 20%);
                }*/
            }

            &:hover {
                background: hsla(224, 20%, 68%, 0.4);
                border: 1px solid darken($score-lighter, 20%);
                cursor: pointer;

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
            width: 5em;
        }

        &.score-number-calc {
            font-size: 1.6rem;
            line-height: 1.4em;
            padding-top: .1em;

            &.mod-criterium {
                background: $score-lighter;
            }
        }
    }

    .icon-score-feedback {
      color: #2787ad;
      font-size: 1.1rem;
      margin-right: .5em;
    }

    .score-result-view {
      margin-left: .5em;
      width: 5em;
    }

    .rr-selected-criterium {
        margin-top: 1em;
        width: 40em;
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
        background: #e4e3e3;
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
        }

        span {
            font-weight: bold;

            &.score-title {
                color: hsla(191, 41%, 33%, 1);
            }
        }
    }

    .rr-selected-criterium-levels {
        background: #e4e3e3;
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
            border-bottom: 1px solid lightgrey;
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
