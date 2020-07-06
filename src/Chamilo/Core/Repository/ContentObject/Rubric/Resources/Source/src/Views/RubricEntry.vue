<template>
    <div id="app" class="entry-app">
        <div v-if="rubric" class="rubric" :class="{'has-evaluator': evaluator !== ''}">
            <link rel="stylesheet"
                  href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
            <div class="rubric-entry-view">
                <div class="table-header-wrap" aria-hidden="true">
                    <ul class="app-header-tools">
                        <li class="app-header-item">Demo:
                            <select v-model="evaluator">
                                <option disabled value="">Selecteer</option>
                                <option v-for="evaluator in evaluators">{{evaluator}}</option>
                            </select>
                        </li>
                        <li class="app-header-item"><button class="btn-check" aria-label="Toon standaard feedback beschrijvingen" :aria-expanded="showDefaultFeedbackFields ? 'true' : 'false'" :class="{ checked: showDefaultFeedbackFields }" @click.prevent="toggleDefaultFeedbackFields"><span tabindex="-1"><i class="check fa" aria-hidden="true" />Feedback</span></button></li>
                    </ul>
                    <div class="table-header">
                        <div v-for="level in rubric.levels" class="table-header-title">
                            {{ level.title }}
                        </div>
                    </div>
                </div>
                <h1 class="rubric-title">{{ rubric.title }}</h1>
                <ul class="clusters" :class="{'show-default-feedback': showDefaultFeedbackFields, 'show-custom-feedback': showDefaultFeedbackFields}">
                    <li v-for="cluster in rubric.clusters" class="cluster-list-item" v-if="rubric.getAllCriteria(cluster).length > 0">
                        <div class="cluster">
                            <h2 class="cluster-title">{{ cluster.title }}</h2>
                            <ul class="categories">
                                <li v-for="category in cluster.categories" class="category-list-item" :style="`--category-color: ${ category.title ? (category.color || '#999') : '#999' }`" v-if="rubric.getAllCriteria(category).length > 0">
                                    <div class="category">
                                        <h3 v-if="category.title" class="category-title category-indicator">{{ category.title }}</h3>
                                        <ul class="criteria">
                                            <li v-for="criterium in category.criteria" role="grid" class="criterium-list-item" :class="{'show-default-feedback': getCriteriumData(criterium).showDefaultFeedback, 'show-custom-feedback': getCriteriumData(criterium).showDefaultFeedback}">
                                                <div class="criterium" role="row">
                                                    <div class="criterium-title-header" role="gridcell">
                                                        <h4 :id="`criterium-${criterium.id}-title`" class="criterium-title category-indicator">{{ criterium.title }}</h4><button v-if="!showDefaultFeedbackFields" class="btn-more" aria-label="Toon standaard feedback beschrijving criterium" :aria-expanded="criterium.showDefaultFeedback ? 'true' : 'false'" @click.prevent="getCriteriumData(criterium).showDefaultFeedback = !getCriteriumData(criterium).showDefaultFeedback"><i tabindex="-1" class="check fa" aria-hidden="true" /></button>
                                                    </div>
                                                    <div v-for="choice in getCriteriumData(criterium).choices" class="criterium-level" role="gridcell" :aria-describedby="`criterium-${criterium.id}-title`">
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
                                                    <textarea v-if="getCriteriumData(criterium).evaluations[evaluator]" placeholder="Geef Feedback" v-model="getCriteriumData(criterium).evaluations[evaluator].feedback" @input="setFeedback(criterium)"></textarea>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
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
                    <div class="rubric-total-title">Maximum:</div><div class="score-wrap"><div class="score-number mod-max">{{ rubric.getMaximumScore() }} <span class="text-hidden">punten</span></div></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import APIConfiguration from '../Connector/APIConfiguration';
    import Rubric, {RubricJsonObject} from '../Domain/Rubric';
    import Level from '../Domain/Level';
    import Cluster from '../Domain/Cluster';
    import Category from '../Domain/Category';
    import Criterium from '../Domain/Criterium';
    import SaveArea from '../Components/SaveArea.vue';
    import DataConnector from '../Connector/DataConnector';

    interface CriteriumExt {
        criterium: Criterium;
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
        private evaluators: string[] = [];
        private criteriaData: CriteriumExt[] = [];

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
            const evaluation = this.getCriteriumData(criterium).evaluations[this.evaluator];
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
                    this.getCriteriumData(criterium).showDefaultFeedback = false;
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
            const criteriumExt = this.getCriteriumData(criterium);
            const criteriumData = this.ensureCriteriumData(criterium);
            criteriumData.feedback = criteriumExt.evaluations[this.evaluator].feedback;
        }

        selectLevel(criterium: Criterium, level: Level) : void {
            if (!this.rubric || !this.evaluator) { return; }
            const criteriumExt = this.getCriteriumData(criterium);
            const evaluation = criteriumExt.evaluations[this.evaluator];
            evaluation.level = level;
            evaluation.score = this.rubric.getChoiceScore(criterium, level);
            const criteriumData = this.ensureCriteriumData(criterium);
            criteriumData.levelId = level.id;
        }

        getCriteriumScore(criterium: Criterium) : number {
            return this.getCriteriumData(criterium).evaluations[this.evaluator]?.score || 0;
        }

        getCategoryScore(category: Category) : number {
            if (!this.rubric) { return 0; }
            return this.rubric.getAllCriteria(category).map(criterium => this.getCriteriumScore(criterium)).reduce(add, 0);
        }

        getClusterScore(cluster: Cluster) : number {
            if (!this.rubric) { return 0; }
            return this.rubric.getAllCriteria(cluster).map(criterium => this.getCriteriumScore(criterium)).reduce(add, 0);
        }

        getRubricScore() : number {
            if (!this.rubric) { return 0; }
            return this.rubric.getAllCriteria().map(criterium => this.getCriteriumScore(criterium)).reduce(add, 0);
        }

        getCriteriumData(criterium: Criterium) : CriteriumExt {
            const criteriumExt = this.criteriaData.find((_ : CriteriumExt) => _.criterium === criterium);
            if (!criteriumExt) { throw new Error(`No data found for criterium: ${criterium}`); }
            return criteriumExt;
        }

        private initData(rubric: Rubric, results: any) {
            this.evaluators = results.evaluators;
            rubric.getAllCriteria().forEach(criterium => {
                const criteriumExt: CriteriumExt = { criterium, choices: [], showDefaultFeedback: false, evaluations: {} };
                rubric.levels.forEach(level => {
                    const choice = rubric.getChoice(criterium, level);
                    const score = rubric.getChoiceScore(criterium, level);
                    criteriumExt.choices.push({ title: level.title, feedback: choice?.feedback || '', score, choice, level});
                });
                const defaultChoice = criteriumExt.choices.find(choice => choice.level.isDefault);
                this.evaluators.forEach((evaluator: any) => {
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
                    criteriumExt.evaluations[evaluator] = criteriumEvaluation;
                    this.criteriaData.push(criteriumExt);
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
            border-top: 1px solid #d6d6d6; /** Added this for result view **/
        }

        .rubric-entry-view {
            position: relative;
        }

        .table-header-wrap {
            display: flex;

            .app-header-tools {
                width: 18.8em;
                min-width: 18.8em;
                background-color: hsla(190, 35%, 75%, 0.2);
                padding-left: 1.2em;
                margin-right: 1em;
            }
        }

        .table-header {
            /*margin-left: 19.8em;*/
            margin-left: 0;
            margin-right: 4em;
            width: 100%;

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

        .rubric-total-max .score-number.mod-max {
            background: hsla(207, 40%, 35%, 1);
            color: #fff;
        }

        .btn-check,
        .table-header,
        .criterium-level,
        .btn-more,
        .subtotal {
            transition: opacity 200ms;
        }

        .rubric:not(.has-evaluator) {
            .btn-check,
            .table-header,
            .criterium-level,
            .btn-more,
            .subtotal {
                /*opacity: 0;*/
                pointer-events: none;
            }
            .table-header, .btn-check, .clusters, .subtotal {
                opacity: 0;
            }
            /*.btn-score-number {
                cursor: not-allowed;
            }
            .subtotal .score-number {
                color: transparent;
            }*/
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
            border: 1px solid #d0d0d0;
            border-radius: $border-radius;
            resize: none;

            &:hover, &:focus {
                border: 1px solid #aaa;
                resize: both;
                outline: none;

                &::placeholder {
                    color: #666;
                }
            }

            &:focus {
                border: 1px solid $input-color-focus;
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
            .rubric-entry-view {
                width: 40em;
            }

            .table-header {
                display: none;
            }

            .criterium {
                flex-direction: column;
                margin-bottom: 2em;
            }

            .criterium-title {
                margin-right: 1.1em;
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

            .criterium-level {
                margin-left: .8em;
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
                margin: -1.5em 1em 1em 1.3em;
            }
        }
    }
    @media only screen and (min-width: 900px) {
        .entry-app .criterium {
            align-items: baseline;

            .criterium-title {
                margin-right: 1.5em;
            }
        }
        .entry-app .criterium-title-header {
            border-top: 1px solid $score-light;
        }
    }
</style>
