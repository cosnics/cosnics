<template>
    <div id="app" class="result-app">
        <div v-if="rubric" class="rubric">
            <link rel="stylesheet"
                  href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
            <div class="rubric-results-view">
                <div class="table-header-wrap">
                    <div class="table-header">
                        <div v-for="evaluator in evaluators" class="table-header-title">{{ evaluator|capitalize }}</div>
                    </div>
                </div>
                <h1 class="rubric-title">{{ rubric.title }}</h1>
                <ul class="clusters">
                    <li v-for="cluster in rubric.clusters" class="cluster-list-item">
                        <div class="cluster">
                            <h2 class="cluster-title">{{ cluster.title }}</h2>
                            <ul class="categories">
                                <li v-for="category in cluster.categories" class="category-list-item" :style="`--category-color: ${ category.title ? category.color : 'none' }`">
                                    <div class="category">
                                        <div v-if="category.title" class="category-title category-indicator">{{ category.title }}</div>
                                        <ul class="criteria">
                                            <li v-for="criterium in category.criteria" class="criterium-list-item" :class="{'show-default-feedback': criterium.showDefaultFeedback, 'show-custom-feedback': criterium.showDefaultFeedback}" @click="selectedCriterium = criterium">
                                                <div class="criterium">
                                                    <div class="criterium-title-header">
                                                        <h4 class="criterium-title category-indicator">{{ criterium.title }}</h4><!--<div v-if="!showDefaultFeedbackFields" class="btn-more" @click.prevent=""><i class="check fa"/></div>-->
                                                    </div>
                                                    <div v-for="evaluator in evaluators" class="subtotal criterium-total">
                                                        <div class="score-number" :id="`${criterium.id}-${evaluator}`" :tabindex="criterium.evaluations[evaluator].feedback ? 0 : -1"><i v-if="criterium.evaluations[evaluator].feedback" class="has-feedback fa fa-info"/>{{
                                                            getCriteriumScore(criterium, evaluator) }}</div>
                                                        <b-tooltip v-if="criterium.evaluations[evaluator].feedback" triggers="hover focus" :target="`${criterium.id}-${evaluator}`" placement="bottom">{{ criterium.evaluations[evaluator].feedback }}</b-tooltip>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <!--<div class="subtotal category-total">
                                        <div class="category-indicator">Totaal {{ category.title }}:</div>
                                        <div v-for="evaluator in evaluators" class="score-wrap">
                                            <div class="score-number">{{ getCategoryScore(category, evaluator) }}</div>
                                        </div>
                                    </div>-->
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
                        <div class="score-number">{{ maximumScore }}</div>
                    </div>
                </div>
            </div>
            <div v-if="selectedCriterium" style="width: 40%; border-left: 1px solid hsla(191, 21%, 80%, 1); margin-left: 1.5em;padding-left: 1.5em; margin-top: 1em;">
                <div style="color: hsla(191, 41%, 38%, 1); margin-left: .25em;margin-bottom: .5em;font-weight: 700;line-height:1.3em">
                    <span>{{ selectedCriterium.parent.parent.title }}<i class="fa fa-angle-right" style="margin: 0 .3em"/></span>
                    <span v-if="selectedCriterium.parent.title.trim().length !== 0">{{ selectedCriterium.parent.title }}<i class="fa fa-angle-right" style="margin: 0 .3em"/></span>
                    <span>{{ selectedCriterium.title }}</span>
                </div>

                <div v-for="evaluator in evaluators" style="background: #d0dddd; margin-bottom: 1em; border-radius: 3px">
                    <p style="margin:0;padding:.25em" v-if="selectedCriterium.evaluations[evaluator].level !== null">{{ evaluator|capitalize }} gaf score {{ getCriteriumScore(selectedCriterium, evaluator) }} ({{selectedCriterium.evaluations[evaluator].level.title}})</p>
                    <p style="margin:0;padding: 0 .25em .25em" v-if="selectedCriterium.evaluations[evaluator].feedback">
                        Extra feedback: {{selectedCriterium.evaluations[evaluator].feedback}}
                    </p>
<!--                    <div class="score-number" :id="`${criterium.id}-${evaluator}`" :tabindex="criterium.evaluations[evaluator].feedback ? 0 : -1"><i v-if="criterium.evaluations[evaluator].feedback" class="has-feedback fa fa-info"/>{{
                        getCriteriumScore(criterium, evaluator) }}</div>
                    <b-tooltip v-if="criterium.evaluations[evaluator].feedback" triggers="hover focus" :target="`${criterium.id}-${evaluator}`" placement="bottom">{{ criterium.evaluations[evaluator].feedback }}</b-tooltip>-->
                </div>

                <div style="font-size: 1.4rem; color: hsla(191, 41%, 38%, 1); margin-left:.15em;margin-top: 2em;margin-bottom:.5em;font-weight:bold">Overzicht niveaus:</div>
                <ul style="list-style: none; margin: 0; padding: 0">
                    <li v-for="level in rubric.levels" style="background: #e3e3e3; border-radius: 3px">
                        <div style="padding: .25em .25em 0; font-weight: 700">{{ level.title }} - {{ rubric.getChoiceScore(selectedCriterium, level) }}</div>

                        <p style="padding: 0em .25em .25em .25em">{{ rubric.getChoice(selectedCriterium, level).feedback }}</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import APIConfiguration from '../Connector/APIConfiguration';
    import TreeNode from '../Domain/TreeNode';
    import Rubric, {RubricJsonObject} from '../Domain/Rubric';
    import Cluster from '../Domain/Cluster';
    import Category from '../Domain/Category';
    import Criterium from '../Domain/Criterium';
    import DataConnector from '../Connector/DataConnector';

    interface CriteriumExt {
        showDefaultFeedback: false;
        evaluations: any;
    }

    function add(v1: number, v2: number) {
        return v1 + v2;
    }

    @Component({
        components: {
        },
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

        @Prop({type: Object, default: null}) readonly rubricData!: any|null;
        @Prop({type: Object, default: null}) readonly apiConfig!: object|null;
        @Prop({type: Number, default: null}) readonly version!: number|null;
        @Prop({type: Object, required: true}) readonly rubricResults!: any;

        getCriteriumScore(criterium: Criterium, evaluator: string) : number {
            return (criterium as unknown as CriteriumExt).evaluations[evaluator].score || 0;
        }

        getCategoryScore(category: Category, evaluator: string) : number {
            return category.criteria.map(criterium => this.getCriteriumScore(criterium, evaluator)).reduce(add, 0);
        }

        getClusterScore(cluster: Cluster, evaluator: string) : number {
            return cluster.categories.map(category => this.getCategoryScore(category, evaluator)).reduce(add, 0);
        }

        getRubricScore(evaluator: string) : number {
            if (!this.rubric) { return 0; }
            return this.rubric.clusters.map(cluster => this.getClusterScore(cluster, evaluator)).reduce(add, 0);
        }

        get maximumScore() : number {
            if (!this.rubric) { return 0; }
            let maxScore = 0;
            this.rubric.getAllCriteria(this.rubric).forEach(criterium => {
                const levelScores = this.rubric!.levels.map(level => this.rubric!.getChoiceScore(criterium, level));
                const max = levelScores.reduce(function(a, b) {
                    return Math.max(a, b);
                });
                maxScore += max;
            });
            return maxScore;
        }

        private getCriteriaRecursive(treeNode: TreeNode, criteria: Criterium[]) {
            treeNode.children.filter(child => (child instanceof Criterium)).forEach(
                criterium => criteria.push(criterium as Criterium)
            );

            treeNode.children.filter(child => child.hasChildren()).forEach(
                child => this.getCriteriaRecursive(child, criteria)
            )
        }

        private initData(rubric: Rubric, results: any) {
            this.evaluators = results.evaluators;
            rubric.getAllCriteria().forEach(criterium => {
                const criteriumExt = criterium as unknown as CriteriumExt;
                Vue.set(criteriumExt, 'showDefaultFeedback', false);
                criteriumExt.evaluations = {};
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

        .criterium-total .score-number:hover {
            background: $score-light;
        }

        .criterium-list-item {
            border: 1px solid transparent;
            border-radius: $border-radius;
        }

        .criterium-list-item:hover {
            background: darken($score-lighter, 10%);
            cursor: pointer;
            border: 1px solid darken($score-lighter, 20%);

            .score-number {
                background: none;
                cursor: pointer;
            }
        }
    }

    @media only screen and (min-width: 900px) {
        .result-app {
            .rubric {
                display: flex;
            }
            .criterium {
                align-items: baseline;
            }
        }
    }
</style>
