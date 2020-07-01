<template>
    <div id="app" class="result-app" >
        <div v-if="rubric" class="rubric">
            <link rel="stylesheet"
                  href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
            <div class="rubric-results-view" @click="selectedCriterium = null">
                <div class="table-header-wrap">
                    <div class="table-header">
                        <div v-for="evaluator in evaluators" class="table-header-title">{{ evaluator|capitalize }}</div>
                    </div>
                </div>
                <h1 class="rubric-title">{{ rubric.title }}</h1>
                <ul class="clusters">
                    <li v-for="cluster in rubric.clusters" class="cluster-list-item" v-if="rubric.getAllCriteria(cluster).length > 0">
                        <div class="cluster">
                            <h2 class="cluster-title">{{ cluster.title }}</h2>
                            <ul class="categories">
                                <li v-for="category in cluster.categories" class="category-list-item" :style="`--category-color: ${ category.title ? category.color : 'none' }`"  v-if="rubric.getAllCriteria(category).length > 0">
                                    <div class="category">
                                        <h3 v-if="category.title" class="category-title category-indicator">{{ category.title }}</h3>
                                        <ul class="criteria">
                                            <li v-for="criterium in category.criteria" class="criterium-list-item" :class="{'show-default-feedback': getCriteriumData(criterium).showDefaultFeedback, 'show-custom-feedback': getCriteriumData(criterium).showDefaultFeedback, selected: selectedCriterium === criterium}" @click.stop="selectedCriterium = criterium">
                                                <div class="criterium">
                                                    <div class="criterium-title-header">
                                                        <h4 class="criterium-title category-indicator">{{ criterium.title }}</h4><!--<div v-if="!showDefaultFeedbackFields" class="btn-more" @click.prevent=""><i class="check fa"/></div>-->
                                                    </div>
                                                    <div v-for="evaluator in evaluators" class="subtotal criterium-total">
                                                        <div class="score-number" :id="`${criterium.id}-${evaluator}`"><i v-if="getCriteriumData(criterium).evaluations[evaluator].feedback" class="has-feedback fa fa-info"/>{{
                                                            getCriteriumScore(criterium, evaluator) }}</div>
                                                        <b-tooltip v-if="getCriteriumData(criterium).evaluations[evaluator].feedback" triggers="hover focus" :target="`${criterium.id}-${evaluator}`" placement="bottom">{{ getCriteriumData(criterium).evaluations[evaluator].feedback }}</b-tooltip>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                            <div class="subtotal cluster-total">
                                <div class="cluster-total-title">Totaal {{ cluster.title }}:</div>
                                <div v-for="evaluator in evaluators" class="score-wrap">
                                    <div class="score-number">{{ getClusterScore(cluster, evaluator) }}</div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="subtotal rubric-total">
                    <div class="rubric-total-title">Totaal Rubric:</div>
                    <div v-for="evaluator in evaluators" class="score-wrap">
                        <div class="score-number">{{ getRubricScore(evaluator) }}</div>
                    </div>
                </div>
                <div class="subtotal rubric-total-max">
                    <div class="rubric-total-title">Maximum score:</div>
                    <div v-for="evaluator in evaluators" class="score-wrap">
                        <div class="score-number">{{ rubric.getMaximumScore() }}</div>
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
                        <p v-if="getCriteriumData(selectedCriterium).evaluations[evaluator].level !== null"><span>{{ evaluator|capitalize }}</span> gaf score <span>{{ getCriteriumScore(selectedCriterium, evaluator) }}</span> (<span class="score-title">{{ getCriteriumData(selectedCriterium).evaluations[evaluator].level.title}}</span>)</p>
                        <p v-if="getCriteriumData(selectedCriterium).evaluations[evaluator].feedback">
                            Extra feedback: {{ getCriteriumData(selectedCriterium).evaluations[evaluator].feedback }}
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
    import APIConfiguration from '../Connector/APIConfiguration';
    import Rubric, {RubricJsonObject} from '../Domain/Rubric';
    import Cluster from '../Domain/Cluster';
    import Category from '../Domain/Category';
    import Criterium from '../Domain/Criterium';
    import DataConnector from '../Connector/DataConnector';

    interface CriteriumExt {
        criterium: Criterium,
        showDefaultFeedback: false;
        evaluations: any;
    }

    function add(v1: number, v2: number) {
        return v1 + v2;
    }

    @Component({
        filters: {
            capitalize: function (value: string) {
                if (!value) return ''
                value = value.toString()
                return value.charAt(0).toUpperCase() + value.slice(1)
            }
        }
    })
    export default class RubricResult extends Vue {
        private rubric: Rubric|null = null;
        private evaluators: string[]|null = null;
        private dataConnector: DataConnector|null = null;
        private selectedCriterium: Criterium|null = null;
        private criteriaData: CriteriumExt[] = [];

        @Prop({type: Object, default: null}) readonly rubricData!: any|null;
        @Prop({type: Object, default: null}) readonly apiConfig!: object|null;
        @Prop({type: Number, default: null}) readonly version!: number|null;
        @Prop({type: Object, required: true}) readonly rubricResults!: any;

        getCriteriumScore(criterium: Criterium, evaluator: string) : number {
            return this.getCriteriumData(criterium).evaluations[evaluator].score || 0;
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

        getCriteriumData(criterium: Criterium) : CriteriumExt {
            const criteriumExt = this.criteriaData.find((_ : CriteriumExt) => _.criterium === criterium);
            if (!criteriumExt) { throw new Error(`No data found for criterium: ${criterium}`); }
            return criteriumExt;
        }

        private initData(rubric: Rubric, results: any) {
            this.evaluators = results.evaluators;
            rubric.getAllCriteria().forEach(criterium => {
                const criteriumExt: CriteriumExt = { criterium: criterium, showDefaultFeedback: false, evaluations: {} };
                this.evaluators!.forEach(evaluator => {
                    const criteriumEvaluation: any = { feedback: '', score: 0, level: null };
                    const evaluations = results.evaluations[evaluator];
                    const criteriumEvaluationInput = evaluations.find((o: any) => o.criteriumId === criterium.id);
                    if (criteriumEvaluationInput) {
                        const chosenLevel = rubric.levels.find(level => level.id === criteriumEvaluationInput.levelId);
                        if (chosenLevel) {
                            criteriumEvaluation.level = chosenLevel;
                            criteriumEvaluation.score = rubric.getChoiceScore(criterium, chosenLevel);
                            criteriumEvaluation.feedback = criteriumEvaluationInput.feedback;
                        }
                    }
                    criteriumExt.evaluations[evaluator] = criteriumEvaluation;
                });
                this.criteriaData.push(criteriumExt);
            });
        }

        mounted() {
            if (this.rubricData) {
                this.rubric = Rubric.fromJSON(this.rubricData as RubricJsonObject);
                this.initData(this.rubric, this.rubricResults);
                // todo: get rubric data id
                this.dataConnector = new DataConnector(this.apiConfig as APIConfiguration, 0, this.version!);
            }
        }
    }
</script>
<style lang="scss">
    .result-app {
        &#app {
            color: $text-color;
            border-top: 1px solid #d6d6d6; /** Added this for result view **/
        }

        .rubric {
            overflow-x: auto;
        }

        .rubric-results-view {
            width: 40em;
            /*max-width: 60em;*/
            position: relative;
        }

        .table-header {
            justify-content: flex-end;

            .table-header-title {
                width: 4.6em;
            }
        }

        .criterium-title-header {
            flex: 1;
        }

        .subtotal {
            margin-right: .5em;
            align-items: baseline;

            .score-wrap {
                width: 5em;
            }

            .score-number {
                font-size: 1.6rem;
                line-height: 1.4em;
                padding-top: .1em;
            }
        }

        .criterium-total {
            width: 5em;
            margin-right: .5em;
            cursor: default;

            .has-feedback {
                font-size: 1.1rem;
                margin-right: .5em;
                color: #2787ad;
            }
        }

        .criterium-list-item {
            border: 1px solid transparent;
            border-radius: $border-radius;

            &.selected {
                background: hsla(224, 20%, 68%, 0.3);
                /*.score-number {
                    background: none;
                    border-bottom: 1px solid darken($score-lighter, 20%);
                }*/
            }
        }

        .criterium-list-item:hover {
            background: hsla(224, 20%, 68%, 0.4);
            cursor: pointer;
            border: 1px solid darken($score-lighter, 20%);

            .score-number {
                /*background: none;*/
                cursor: pointer;
                /*border-bottom: 1px solid darken($score-lighter, 20%);*/
            }
        }
    }

    .rr-selected-criterium {
        margin-top: 1em;
        width: 40em;
    }
    @media only screen and (min-width: 900px) {
        .result-app {
            .rr-selected-criterium {
                border-left: 1px solid hsla(191, 21%, 80%, 1);
                width: 40%;
                margin-left: 1.5em;
                padding-left: 1.5em;
            }
        }
    }

    .rr-selected-criterium-results {
        background: #e4e3e3;
        padding: .5em;
        border-radius: $border-radius;

        .title {
            color: hsla(191, 41%, 38%, 1);
            margin-bottom: .5em;
            font-weight: 700;
            line-height: 1.3em;

            .separator {
                margin: 0 .3em;
            }
        }
    }

    .rr-selected-result {
        margin-bottom: 1em;
        border-radius: $border-radius;

        p {
            margin:0;
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
        padding: .5em;
        margin-top: 1.5em;

        .title {
            font-size: 1.4rem;
            margin-top: 0;
            margin-bottom: 0;
            font-weight: bold;
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
            display: flex;
            width: 100%;
            align-items: baseline;
            border-bottom: 1px solid lightgrey;

            .title {
                flex: 1;
                font-weight: 700;
            }

            .choice-score {
                text-align: right;
                font-size: 2rem;
            }
            .choice-feedback {
                margin: .25em 1.5em 1.25em 0;
            }
        }
    }

    @media only screen and (min-width: 900px) {
        .result-app {
            .rubric {
                display: flex;
            }
            .criterium {
                align-items: center;
            }
        }
    }
</style>
