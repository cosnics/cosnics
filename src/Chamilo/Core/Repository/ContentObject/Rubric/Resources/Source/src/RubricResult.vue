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
                                <li v-for="category in cluster.categories" class="category-list-item" :style="`--category-color: ${category.color}`">
                                    <div class="category">
                                        <div class="category-title category-indicator">{{ category.title }}</div>
                                        <ul class="criteria">
                                            <li v-for="criterium in category.criteria" class="criterium-list-item" :class="{'show-default-feedback': criterium.showDefaultFeedback, 'show-custom-feedback': criterium.showDefaultFeedback}">
                                                <div class="criterium">
                                                    <div class="criterium-title-header">
                                                        <h4 class="criterium-title category-indicator">{{ criterium.title }}</h4><!--<div v-if="!showDefaultFeedbackFields" class="btn-more" @click.prevent=""><i class="check fa"/></div>-->
                                                    </div>
                                                    <div v-for="evaluator in evaluators" class="subtotal criterium-total">
                                                        <div class="score-number" :id="`${criterium.id}-${evaluator}`"><i v-if="criterium.evaluations[evaluator].feedback" class="has-feedback fa fa-info"/>{{
                                                            getCriteriumScore(criterium, evaluator) }}</div>
                                                        <b-tooltip v-if="criterium.evaluations[evaluator].feedback" triggers="hover" :target="`${criterium.id}-${evaluator}`" placement="bottom">{{ criterium.evaluations[evaluator].feedback }}</b-tooltip>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="subtotal category-total">
                                        <div class="category-indicator">Totaal {{ category.title }}:</div>
                                        <div v-for="evaluator in evaluators" class="score-wrap">
                                            <div class="score-number">{{ getCategoryScore(category, evaluator) }}</div>
                                        </div>
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
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import APIConfiguration from './Connector/APIConfiguration';
    import TreeNode from './Domain/TreeNode';
    import Rubric, {RubricJsonObject} from './Domain/Rubric';
    import Cluster from './Domain/Cluster';
    import Category from './Domain/Category';
    import Criterium from './Domain/Criterium';
    import DataConnector from './Connector/DataConnector';

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
            border-top: 1px solid $panel-border-color; /** Added this for result view **/
        }

        .rubric-results-view {
            position: relative;
            width: 100%;
            max-width: 40em;
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
    }
</style>
