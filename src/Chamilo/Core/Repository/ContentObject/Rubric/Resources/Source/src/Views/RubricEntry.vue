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
                <li class="app-header-item"><button class="btn-check" aria-label="Toon standaard feedback beschrijvingen" :aria-expanded="showDefaultFeedbackFields ? 'true' : 'false'" :class="{ checked: showDefaultFeedbackFields }" @click.prevent="toggleDefaultFeedbackFields"><span tabindex="-1"><i class="check fa" aria-hidden="true" />Feedback</span></button></li>
            </ul>
            <save-area :data-connector="dataConnector"></save-area>
        </div>
        <div v-if="rubric" class="rubric" :class="{'has-evaluator': evaluator !== ''}">
            <link rel="stylesheet"
                  href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
            <div class="rubric-entry-view">
                <div class="table-header-wrap" aria-hidden="true">
                    <div class="table-header">
                        <div v-for="level in rubric.levels" class="table-header-title">
                            {{ level.title }}
                        </div>
                    </div>
                </div>
                <h1 class="rubric-title">{{ rubric.title }}</h1>
                <ul class="clusters" :class="{'show-default-feedback': showDefaultFeedbackFields, 'show-custom-feedback': showDefaultFeedbackFields}">
                    <li v-for="cluster in rubric.clusters" class="cluster-list-item">
                        <div class="cluster">
                            <h2 class="cluster-title">{{ cluster.title }}</h2>
                            <ul class="categories">
                                <li v-for="category in cluster.categories" class="category-list-item" :style="`--category-color: ${ category.title ? category.color : 'none' }`">
                                    <div class="category">
                                        <div v-if="category.title" class="category-title category-indicator">{{ category.title }}</div>
                                        <ul class="criteria">
                                            <li v-for="criterium in category.criteria" role="grid" class="criterium-list-item" :class="{'show-default-feedback': criterium.showDefaultFeedback, 'show-custom-feedback': criterium.showDefaultFeedback}">
                                                <div class="criterium" role="row" >
                                                    <div class="criterium-title-header" role="gridcell">
                                                        <h4 :id="`criterium-${criterium.id}-title`" class="criterium-title category-indicator">{{ criterium.title }}</h4><button v-if="!showDefaultFeedbackFields" class="btn-more" aria-label="Toon standaard feedback beschrijving criterium" :aria-expanded="criterium.showDefaultFeedback ? 'true' : 'false'" @click.prevent="criterium.showDefaultFeedback = !criterium.showDefaultFeedback"><i tabindex="-1" class="check fa" aria-hidden="true" /></button>
                                                    </div>
                                                    <div v-for="choice in criterium.choices" class="criterium-level" role="gridcell" :aria-describedby="`criterium-${criterium.id}-title`">
                                                        <button role="radio" :aria-checked="isSelected(criterium, choice.level)" class="criterium-level-header btn-score-number" :class="{ selected: isSelected(criterium, choice.level) }" @click="selectLevel(criterium, choice.level)">
                                                            <div class="criterium-level-title">
                                                                {{choice.title}}
                                                            </div>
                                                            <span class="score-number" :aria-label="`${ choice.score } punten`"><!--<i class="check fa"/>-->{{ choice.score }}</span>
                                                        </button>
                                                        <div class="default-feedback">
                                                            {{ choice.feedback }}
                                                        </div>
                                                    </div>
                                                    <div class="subtotal criterium-total" role="gridcell" :aria-describedby="`criterium-${criterium.id}-title`">
                                                        <div class="score-number"><span class="text-hidden">Totaal:</span> {{ getCriteriumScore(criterium) }} <span class="text-hidden">punten</span></div>
                                                    </div>
                                                </div>
                                                <div class="custom-feedback">
                                                    <textarea v-if="criterium.evaluations[evaluator]" placeholder="Geef Feedback" v-model="criterium.evaluations[evaluator].feedback" @input="setFeedback(criterium)"></textarea>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <!--<div class="subtotal category-total">
                                        <div class="category-indicator">Totaal {{ category.title }}:</div><div class="score-wrap"><div class="score-number">{{ getCategoryScore(category) }} <span class="text-hidden">punten</span></div></div>
                                    </div>-->
                                </li>
                            </ul>
                            <div class="subtotal cluster-total">
                                <div class="cluster-total-title">Totaal {{ cluster.title }}:</div><div class="score-wrap"><div class="score-number">{{ getClusterScore(cluster) }} <span class="text-hidden">punten</span></div></div>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="subtotal rubric-total">
                    <div class="rubric-total-title">Totaal Rubric:</div><div class="score-wrap"><div class="score-number">{{ getRubricScore() }} <span class="text-hidden">punten</span></div></div>
                </div>
                <div class="subtotal rubric-total-max">
                    <div class="rubric-total-title">Maximum:</div><div class="score-wrap"><div class="score-number">{{ maximumScore }} <span class="text-hidden">punten</span></div></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import APIConfiguration from '../Connector/APIConfiguration';
    import TreeNode from '../Domain/TreeNode';
    import Rubric, {RubricJsonObject} from '../Domain/Rubric';
    import Level from '../Domain/Level';
    import Cluster from '../Domain/Cluster';
    import Category from '../Domain/Category';
    import Criterium from '../Domain/Criterium';
    import SaveArea from '../Components/SaveArea.vue';
    import DataConnector from '../Connector/DataConnector';

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
            SaveArea
        },
    })
    export default class RubricEntry extends Vue {
        private dataConnector: DataConnector|null = null;
        private rubric: Rubric|null = null;
        private evaluators: string[]|null = null;
        //private showDefaultFeedbackFields = false; // Set through uiState
        //private evaluator = ''; // Set through uiState

        @Prop({type: Object, default: null}) readonly rubricData!: object|null;
        @Prop({type: Object, default: null}) readonly apiConfig!: object|null;
        @Prop({type: Number, default: null}) readonly version!: number|null;
        @Prop({type: Object, required: true}) readonly rubricResults!: any;
        @Prop({type: Object}) readonly uiState!: any;

        get evaluator() {
            return this.uiState.evaluator;
        }

        set evaluator(evaluator: string) {
            this.uiState.evaluator = evaluator;
        }

        isSelected(criterium: Criterium, level: Level) {
            const isDefaultLevel = level.isDefault;
            if (!this.evaluator) { return isDefaultLevel; }
            const evaluation = (criterium as unknown as CriteriumExt).evaluations[this.evaluator];
            if (!evaluation.level) { return isDefaultLevel; }
            return evaluation.level === level;
        }

        get showDefaultFeedbackFields() : boolean {
            return this.uiState.showDefaultFeedbackFields;
        }

        toggleDefaultFeedbackFields() {
            this.uiState.showDefaultFeedbackFields = !this.uiState.showDefaultFeedbackFields;
            if (!this.uiState.showDefaultFeedbackFields) {
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
    .text-hidden {
        position: absolute;
        top: auto;
        left: -10000px;
        width: 1px;
        height: 1px;
        opacity: 0;
    }

    .entry-app {
        &#app {
            color: $text-color;
        }

        .rubric-entry-view {
            position: relative;
        }

        .table-header {
            margin-left: 19.8em;
            margin-right: 4em;

            .table-header-title {
                flex: 1;
            }
        }

        .criterium-title-header {
            width: 19em;
            min-width: 19em;
        }

        .criterium-level:nth-last-child(2) {
            margin-right: 1em;
        }

        .criterium-level-header {
            width: 100%;
        }

        .criterium-level-header.selected {
            background: $level-selected-color;
        }

        .btn-score-number {
            outline: none;
        }

        .has-evaluator .btn-score-number {
            &:hover, &:focus {
                border: 1px solid $level-selected-color;
            }

            &.selected {
                &:hover, &:focus {
                    box-shadow: inset 0 0 0 1px white;
                }
            }
        }

        .subtotal {
            .score-wrap {
                width: 3.5em;
                margin-left: 1em;
            }
        }

        .criterium-total {
            min-width: 3.5em;
            height: 1.58em;

            .score-number {
                background: $score-lighter;
                border-radius: $border-radius;
                padding-top: 1px;
                margin-bottom: -1px;
            }
        }

        .rubric:not(.has-evaluator) {
            .btn-score-number {
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

    @media only screen and (max-width: 899px) {
        .entry-app {
            .table-header-wrap {
                display: none;
            }

            .criterium {
                flex-direction: column;
                margin-bottom: 2em;

                .criterium-level-title {
                    display: block;
                    flex: 1;
                    line-height: 2.4em;
                }
            }

            .criterium-title-header {
                width: unset;
                min-width: unset;
                max-width: 40em;
            }

            .criterium-level-header {
                display: flex;
                text-align: left;
                align-content: center;
                justify-items: center;
                justify-content: center;
                padding: 0 .25em;
                margin-top: .5em;
                max-width: 40em;

                &.selected .criterium-level-title {
                    color: white;
                }
            }

            .criterium-level:nth-last-child(2) {
                margin-right: .5em;
            }

            .btn-score-number.score-number {
                flex: 0;
            }

            .subtotal {
                max-width: 41.25em;
                margin-right: .5em;
            }

            .criterium-total {
                display: none;
            }

            .default-feedback {
                max-width: 40em;
            }

            .custom-feedback {
                margin-top: 1.5em;
                margin-left: 1.5em;
            }
        }

        @media only screen and (min-width: 900px) {
            .entry-app .criterium {
                align-items: baseline;
            }
        }
    }
</style>
