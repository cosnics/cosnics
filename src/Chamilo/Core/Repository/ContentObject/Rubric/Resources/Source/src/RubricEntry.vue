<template>
    <div id="app" class="entry-app">
        <div class="app-header">
            <div class="app-header-menu"></div>
            <ul class="app-header-tools">
                <li class="app-header-item">Ik ben
                    <select v-model="evaluator">
                        <option disabled value="">Selecteer</option>
                        <option v-for="evaluator in evaluators">{{evaluator}}</option>
                    </select>
                </li>
                <li class="app-header-item" :class="{ checked: showDefaultFeedbackFields }"><a role="button" @click.prevent="toggleDefaultFeedbackFields"><i class="check fa" />Feedback</a></li>
            </ul>
            <div class="save-state">
                <div v-if="dataConnector && dataConnector.isSaving" class="saving">
                    Processing {{dataConnector.processingSize}} saves...
                </div>
                <div v-else-if="dataConnector" class="saved" role="alert">
                    All changes saved
                </div>
            </div>
        </div>
        <div v-if="rubric" class="rubric" :class="{'has-evaluator': evaluator !== ''}">
            <link rel="stylesheet"
                  href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
            <div class="rubric-entry-view">
                <div class="table-header-wrap">
                    <div class="table-header">
                        <div v-for="level in rubric.levels" class="table-header-title">
                            {{level.title}}
                        </div>
                    </div>
                </div>
                <h1 class="rubric-title">{{ rubric.title }}</h1>
                <ul class="clusters" :class="{'show-default-feedback': showDefaultFeedbackFields, 'show-custom-feedback': showDefaultFeedbackFields}">
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
                                                        <h4 class="criterium-title category-indicator">{{ criterium.title }}</h4><div v-if="!showDefaultFeedbackFields" class="btn-more" @click.prevent="criterium.showDefaultFeedback = !criterium.showDefaultFeedback"><i class="check fa"/></div>
                                                    </div>
                                                    <div v-for="choice in criterium.choices" class="criterium-level">
                                                        <div class="criterium-level-header" tabindex="0" @keyup.enter.space.stop="selectLevel(criterium, choice.level)" @click="selectLevel(criterium, choice.level)" :class="{ selected: isSelected(criterium, choice.level) }">
                                                            <div class="criterium-level-title">
                                                                {{choice.title}}
                                                            </div>
                                                            <div class="score-number"><!--<i class="check fa"/>-->{{ choice.score }}</div>
                                                        </div>
                                                        <div class="default-feedback">
                                                            {{ choice.feedback }}
                                                        </div>
                                                    </div>
                                                    <div class="subtotal criterium-total">
                                                        <div class="score-number">{{ getCriteriumScore(criterium) }}</div>
                                                    </div>
                                                </div>
                                                <div class="custom-feedback">
                                                    <textarea v-if="criterium.evaluations[evaluator]" placeholder="Geef Feedback" v-model="criterium.evaluations[evaluator].feedback" @input="setFeedback(criterium)"></textarea>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="subtotal category-total">
                                        <div class="category-indicator">Totaal {{ category.title }}:</div><div class="score-wrap"><div class="score-number">{{ getCategoryScore(category) }}</div></div>
                                    </div>
                                </li>
                            </ul>
                            <div class="subtotal cluster-total">
                                <div class="cluster-total-title">Totaal {{ cluster.title }}:</div><div class="score-wrap"><div class="score-number">{{ getClusterScore(cluster) }}</div></div>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="subtotal rubric-total">
                    <div class="rubric-total-title">Totaal Rubric:</div><div class="score-wrap"><div class="score-number">{{ getRubricScore() }}</div></div>
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
    import Level from './Domain/Level';
    import Cluster from './Domain/Cluster';
    import Category from './Domain/Category';
    import Criterium from './Domain/Criterium';
    import DataConnector from './Connector/DataConnector';

    interface CriteriumExt {
        choices: any[];
        showDefaultFeedback: false;
        evaluations: any;
    }

    function add(v1: number, v2: number) {
        return v1 + v2;
    }

    @Component({
        components: {
        },
    })
    export default class RubricEntry extends Vue {
        private dataConnector: DataConnector|null = null;
        private rubric: Rubric|null = null;
        private showDefaultFeedbackFields = false;
        private evaluators: string[]|null = null;
        private evaluator = '';

        @Prop({type: Object, default: null}) readonly rubricData!: object|null;
        @Prop({type: Object, default: null}) readonly apiConfig!: object|null;
        @Prop({type: Number, default: null}) readonly version!: number|null;
        @Prop({type: Object, required: true}) readonly rubricResults!: any;

        isSelected(criterium: Criterium, level: Level) {
            const isDefaultLevel = level.isDefault;
            if (!this.evaluator) { return isDefaultLevel; }
            const evaluation = (criterium as unknown as CriteriumExt).evaluations[this.evaluator];
            if (!evaluation.level) { return isDefaultLevel; }
            return evaluation.level === level;
        }

        toggleDefaultFeedbackFields() {
            this.showDefaultFeedbackFields = !this.showDefaultFeedbackFields;
            if (!this.showDefaultFeedbackFields) {
                this.rubric!.getAllCriteria().forEach(criterium => {
                    const criteriumExt = criterium as unknown as CriteriumExt;
                    criteriumExt.showDefaultFeedback = false;
                });
            }
        }

        ensureCriteriumData(criterium: Criterium) : any {
            if (!this.evaluator) { return; }
            const evaluations = this.rubricResults.evaluations[this.evaluator];
            let evaluation = evaluations.find((ev: any) => ev.criteriumId === criterium.id);
            if (!evaluation) {
                evaluation = { criteriumId: criterium.id, levelId: null, feedback: '' };
                evaluations.push(evaluation);
            }
            return evaluation;
        }

        setFeedback(criterium: Criterium) : void {
            if (!this.evaluator) { return; }
            const criteriumExt = criterium as unknown as CriteriumExt;
            const criteriumData = this.ensureCriteriumData(criterium);
            criteriumData.feedback = criteriumExt.evaluations[this.evaluator].feedback;
        }

        selectLevel(criterium: Criterium, level: Level) : void {
            if (!this.rubric || !this.evaluator) { return; }
            const criteriumExt = criterium as unknown as CriteriumExt;
            const evaluation = criteriumExt.evaluations[this.evaluator];
            evaluation.level = level;
            evaluation.score = this.rubric.getChoiceScore(criterium, level);
            const criteriumData = this.ensureCriteriumData(criterium);
            criteriumData.levelId = level.id;
        }

        getCriteriumScore(criterium: Criterium) : number {
            return (criterium as unknown as CriteriumExt).evaluations[this.evaluator]?.score || 0;
        }

        getCategoryScore(category: Category) : number {
            return category.criteria.map(criterium => this.getCriteriumScore(criterium)).reduce(add, 0);
        }

        getClusterScore(cluster: Cluster) : number {
            return cluster.categories.map(category => this.getCategoryScore(category)).reduce(add, 0);
        }

        getRubricScore() : number {
            if (!this.rubric) { return 0; }
            return this.rubric.clusters.map(cluster => this.getClusterScore(cluster)).reduce(add, 0);
        }

        private getCriteriaRecursive(treeNode: TreeNode, criteria: Criterium[]) {
            treeNode.children.filter(child => (child instanceof Criterium)).forEach(
                criterium => criteria.push(criterium as Criterium)
            );

            treeNode.children.filter(child => child.hasChildren()).forEach(
                child => this.getCriteriaRecursive(child, criteria)
            )
        }

        get populatedClusters() {
            return this.rubric!.clusters.filter((cluster: Cluster) => {
                const criteria: Criterium[] = [];
                this.getCriteriaRecursive(cluster, criteria);
                return criteria.length !== 0;
            });
        }

        private initData(rubric: Rubric, results: any) {
            this.evaluators = results.evaluators;
            rubric.getAllCriteria().forEach(criterium => {
                const criteriumExt = criterium as unknown as CriteriumExt;
                criteriumExt.choices = [];
                Vue.set(criteriumExt, 'showDefaultFeedback', false);
                Vue.set(criteriumExt, 'evaluations', {});
                rubric.levels.forEach(level => {
                    const choice = rubric.getChoice(criterium, level);
                    const score = rubric.getChoiceScore(criterium, level);
                    criteriumExt.choices.push({ title: level.title, feedback: choice?.feedback || '', score, choice, level});
                });
                const defaultChoice = criteriumExt.choices.find(choice => choice.level.isDefault);
                this.evaluators!.forEach(evaluator => {
                    const criteriumEvaluation: any = { feedback: '', score: defaultChoice ? defaultChoice.score : 0, level: defaultChoice ? defaultChoice.level : null };
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
                    Vue.set(criteriumExt.evaluations, evaluator, criteriumEvaluation);
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
    .entry-app {
        &#app {
            color: $text-color;
        }

        .rubric-entry-view {
            position: relative;
        }

        .table-header {
            margin-left: 19.8em;
            margin-right: 3.5em;

            .table-header-title {
                flex: 1;
            }
        }

        .criterium-title-header {
            width: 18em;
            min-width: 18em;
        }

        .criterium-level-header {
            &:hover, &:focus, &.selected {
                outline: none;
            }

            &.selected {
                &, &:focus {
                    background: $level-selected-color;
                }
            }
        }

        .has-evaluator .criterium-level-header {
            &:hover, &:focus, &.selected {
                border: 1px solid $level-selected-color;
            }

            &.selected {
                &:hover, &:focus {
                    border-color: $level-selected-color-dark;
                }
            }
        }

        .subtotal {
            .score-wrap {
                width: 3.5em;
            }
        }

        .criterium-total {
            min-width: 3.5em;
        }

        .rubric:not(.has-evaluator) {
            .criterium-level-header {
                cursor: not-allowed;
            }
            .subtotal .score-number {
                color: transparent;
            }
        }
    }

    .custom-feedback {
        margin-left: 20em;
        margin-bottom: 1em;
        display: none;

        textarea {
            padding: .2em .4em 0;
            width: 40em;
            height: 2.2em;
            max-width: 100%;
            background: transparent;
            border: 1px solid #d0d0d0;
            border-radius: $border-radius;
            resize: none;

            &:hover, &:focus {
                border: 1px solid #aaa;
                background: white;
                resize: both;

                &::placeholder {
                    color: #666;
                }
            }

            &::placeholder {
                opacity: 1;
                color: #aaa;
            }
        }
    }
    .show-custom-feedback .custom-feedback {
        display: block;
    }
    .show-default-feedback .custom-feedback {
        margin-left: 20em;
    }
</style>
