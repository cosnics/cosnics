<template>
    <div id="app" class="entry-app">
        <div class="rubric" :class="{'is-demo': options.isDemo, 'is-evaluator-selected': options.evaluator !== ''}">
            <link rel="stylesheet"
                  href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
            <div class="rubric-entry-view">
                <div class="table-header-wrap" aria-hidden="true">
                    <ul class="app-header-tools">
                        <slot name="demoEvaluator"></slot>
                        <li class="app-header-item"><button class="btn-check" aria-label="Toon standaard feedback beschrijvingen" :aria-expanded="showDefaultFeedbackFields ? 'true' : 'false'" :class="{ checked: showDefaultFeedbackFields }" @click.prevent="toggleDefaultFeedbackFields"><span tabindex="-1"><i class="check fa" aria-hidden="true" />{{ options.isDemo ? 'Feedback' : 'Feedback beschrijvingen'}}</span></button></li>
                    </ul>
                    <div class="table-header">
                        <div v-for="level in rubric.levels" class="table-header-title">
                            {{ level.title }}
                        </div>
                    </div>
                </div>
                <h1 class="rubric-title">{{ rubric.title }}</h1>
                <ul class="clusters" :class="{'is-feedback-visible': showDefaultFeedbackFields}">
                    <li v-for="cluster in rubric.clusters" class="cluster-list-item" v-if="rubric.getAllCriteria(cluster).length > 0">
                        <div class="cluster">
                            <h2 class="cluster-title">{{ cluster.title }}</h2>
                            <ul class="categories">
                                <li v-for="category in cluster.categories" class="category-list-item" :style="`--category-color: ${ category.title ? (category.color || '#999') : '#999' }`" v-if="rubric.getAllCriteria(category).length > 0">
                                    <div class="category">
                                        <h3 v-if="category.title" class="category-title category-indicator">{{ category.title }}</h3>
                                        <ul class="criteria">
                                            <criterium-entry v-for="criterium in category.criteria"
                                                 tag="li" class="criterium-list-item"
                                                 :key="`criterium-${criterium.id}-key`"
                                                 :show-default-feedback-fields="showDefaultFeedbackFields"
                                                 :criterium="criterium"
                                                 :preview="preview"
                                                 :ext="getCriteriumData(criterium)"
                                                 :evaluation="getCriteriumEvaluation(criterium)"
                                                 :show-errors="showErrors"
                                                 @level-selected="selectLevel" @feedback-changed="onCriteriumFeedbackChanged">
                                            </criterium-entry>
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
    import Rubric from '../Domain/Rubric';
    import Level from '../Domain/Level';
    import Cluster from '../Domain/Cluster';
    import Category from '../Domain/Category';
    import Criterium from '../Domain/Criterium';
    import {CriteriumEvaluation, CriteriumExt} from '../Util/interfaces';
    import CriteriumEntry from '../Components/CriteriumEntry.vue';

    function add(v1: number, v2: number) {
        return v1 + v2;
    }

    @Component({
        components: { CriteriumEntry }
    })
    export default class RubricEntry extends Vue {
        private criteriaData: CriteriumExt[] = [];

        @Prop({type: Rubric}) readonly rubric!: Rubric;
        @Prop({type: Array, default: () => []}) readonly criteriumEvaluations!: CriteriumEvaluation[];
        @Prop({type: Object}) readonly uiState!: any;
        @Prop({type: Object, default: () => ({})}) readonly options!: any;
        @Prop({type: Boolean, default: false}) readonly preview!: boolean;
        @Prop({type: Boolean, default: false}) readonly showErrors!: boolean;

        get showDefaultFeedbackFields() : boolean {
            return this.uiState.showDefaultFeedbackFields;
        }

        toggleDefaultFeedbackFields() {
            const show = this.uiState.showDefaultFeedbackFields = !this.uiState.showDefaultFeedbackFields;
            if (!show) {
                this.rubric.getAllCriteria().forEach(criterium => {
                    this.getCriteriumData(criterium)!.showDefaultFeedback = false;
                });
            }
        }

        onCriteriumFeedbackChanged(evaluation: CriteriumEvaluation) : void {
            if (!evaluation) { return; }
            this.$emit('criterium-feedback-changed', evaluation.criterium, evaluation.feedback);
        }

        isSelected(criterium: Criterium, level: Level) {
            const isDefaultLevel = level.isDefault;
            if (!this.criteriumEvaluations) { return isDefaultLevel; }
            const evaluation = this.criteriumEvaluations.find(evaluation => evaluation.criterium === criterium);
            if (!evaluation || !evaluation.level) {
                return isDefaultLevel;
            }
            return evaluation.level === level;
        }

        selectLevel(evaluation: CriteriumEvaluation, level: Level) : void {
            evaluation.level = level;
            evaluation.score = this.rubric.getChoiceScore(evaluation.criterium, level);
            this.$emit('level-selected', evaluation.criterium, level);
        }

        getCriteriumScore(criterium: Criterium) : number {
            if (this.preview) { return 0; }
            const evaluation = this.criteriumEvaluations.find(evaluation => evaluation.criterium === criterium);
            if (!evaluation) { return 0; }
            return evaluation.score || 0;
        }

        getCategoryScore(category: Category) : number {
            if (this.preview) { return 0; }
            return this.rubric.getAllCriteria(category).map(criterium => this.getCriteriumScore(criterium)).reduce(add, 0);
        }

        getClusterScore(cluster: Cluster) : number {
            if (this.preview) { return 0; }
            return this.rubric.getAllCriteria(cluster).map(criterium => this.getCriteriumScore(criterium)).reduce(add, 0);
        }

        getRubricScore() : number {
            if (this.preview) { return 0; }
            return this.rubric.getAllCriteria().map(criterium => this.getCriteriumScore(criterium)).reduce(add, 0);
        }

        getCriteriumData(criterium: Criterium) : CriteriumExt|null {
            return this.criteriaData.find((_ : CriteriumExt) => _.criterium === criterium) || null;
        }

        getCriteriumEvaluation(criterium: Criterium) : CriteriumEvaluation|null {
            return this.criteriumEvaluations.find((_ : CriteriumEvaluation) => _.criterium === criterium) || null;
        }

        private initData() {
            const rubric = this.rubric;
            this.criteriaData = rubric.getAllCriteria().map(criterium => {
                const choices = rubric.levels.map(level => {
                    const choice = rubric.getChoice(criterium, level);
                    const score = rubric.getChoiceScore(criterium, level);
                    return { title: level.title, feedback: choice?.feedback || '', score, choice, level};
                });
                return { criterium, choices, showDefaultFeedback: false };
            });
        }

        created() {
            this.initData();
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

            &.is-selected {
              background: $level-selected-color;
            }
        }

        .score-number.is-selected {
            color: #fff;
        }

        .btn-score-number {
            outline: none;
            cursor: pointer;

            &:hover, &:focus {
                border: 1px solid $level-selected-color;
            }

            &.is-selected {
                &:hover, &:focus {
                    box-shadow: inset 0 0 0 1px white;
                }
            }
        }

        .subtotal .score-wrap {
            width: 3.5em;
            margin-left: 1em;
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

        .rubric.is-demo {
            .btn-check,
            .table-header,
            .criterium-level,
            .btn-more,
            .subtotal {
                transition: opacity 200ms;
            }
        }

        .rubric.is-demo:not(.is-evaluator-selected) {
            .btn-check,
            .table-header,
            .criterium-level,
            .btn-more,
            .subtotal {
                pointer-events: none;
            }
            .table-header, .btn-check, .clusters, .subtotal {
                opacity: 0;
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

    .is-feedback-visible .custom-feedback {
        display: block;
        margin-left: 20em;
    }

    .rubric-entry-error {
      border-bottom: 2px solid red;
      color: red;
      margin-left: 19.8em;
      margin-right: 4.5em;
      padding: 0 .25em;
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
