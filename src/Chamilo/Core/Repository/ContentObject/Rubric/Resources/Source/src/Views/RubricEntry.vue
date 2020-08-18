<i18n>
{
    "en": {
        "feedback": "Feedback",
        "feedback-descriptions": "Feedback descriptions",
        "points": "points",
        "rubric": "Rubric",
        "show-default-descriptions": "Show default feedback descriptions",
        "total": "Total"
    },
    "fr": {
        "feedback": "Feed-back",
        "feedback-descriptions": "Feed-back descriptions",
        "points": "points",
        "rubric": "Rubrique",
        "show-default-descriptions": "Afficher descriptions feed-back standard",
        "total": "Total"
    },
    "nl": {
        "feedback": "Feedback",
        "feedback-descriptions": "Feedback beschrijvingen",
        "points": "punten",
        "rubric": "Rubric",
        "show-default-descriptions": "Toon standaard feedback beschrijvingen",
        "total": "Totaal"
    }
}
</i18n>

<template>
    <div id="app" class="mod-sep">
        <div class="rubric">
            <link rel="stylesheet"
                  href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
            <div class="rubric-entry-view">
                <div class="rubric-table-header mod-entry-view" aria-hidden="true">
                    <ul class="app-header-tools mod-entry-view">
                        <slot name="demoEvaluator"></slot>
                        <li class="app-tool-item" :class="{ 'is-demo-inactive': this.options.isDemo && !this.options.evaluator }"><button class="btn-check" :aria-label="$t('show-default-descriptions')" :aria-expanded="showDefaultFeedbackFields ? 'true' : 'false'" :class="{ checked: showDefaultFeedbackFields }" @click.prevent="toggleDefaultFeedbackFields"><span class="lbl-check" tabindex="-1"><i class="btn-icon-check fa" aria-hidden="true" />{{ options.isDemo ? $t('feedback') : $t('feedback-descriptions') }}</span></button></li>
                    </ul>
                    <div class="levels-table-header mod-entry-view" :class="{ 'is-demo-inactive': this.options.isDemo && !this.options.evaluator }">
                        <div v-for="level in rubric.levels" class="level-table-header-title">
                            {{ level.title }}
                        </div>
                    </div>
                </div>
                <div class="rubric-table" :class="{ 'is-demo-inactive': this.options.isDemo && !this.options.evaluator }">
                    <h1 class="rubric-title">{{ rubric.title }}</h1>
                    <ul class="clusters">
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
                                <div class="subtotal cluster-total mod-entry-view">
                                    <div class="cluster-total-title">{{ $t('total') }} {{ cluster.title }}:</div><div class="score-entry-view"><div class="score-number-calc mod-cluster">{{ getClusterScore(cluster) }} <span class="text-hidden">{{ $t('points') }}</span></div></div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="subtotal rubric-total mod-entry-view">
                        <div class="rubric-total-title">{{ $t('total') }} {{ $t('rubric') }}:</div><div class="score-entry-view"><div class="score-number-calc mod-rubric">{{ getRubricScore() }} <span class="text-hidden">{{ $t('points') }}</span></div></div>
                    </div>
                    <div class="subtotal rubric-total-max mod-entry-view">
                        <div class="rubric-total-title">Maximum:</div><div class="score-entry-view"><div class="score-number-calc mod-rubric-max">{{ rubric.getMaximumScore() }} <span class="text-hidden">{{ $t('points') }}</span></div></div>
                    </div>
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
        height: 1px;
        left: -10000px;
        opacity: 0;
        position: absolute;
        top: auto;
        width: 1px;
    }

    .rubric-entry-view {
        position: relative;
    }

    .mod-entry-view {
        &.rubric-table-header {
            display: flex;
        }

        &.app-header-tools {
            background-color: hsla(190, 35%, 75%, 0.2);
            margin-right: 1em;
            min-width: 18.8em;
            padding-left: 1.2em;
            width: 18.8em;
        }

        &.levels-table-header {
            margin-left: 0;
            margin-right: 4em;
            width: 100%;
        }

        &.criterium-title-header {
            border-top: 1px solid $score-light;
        }

        &.criterium-title {
            margin-right: 1.5em;
        }

        &.criterium-level-header {
            width: 100%;

            &.is-selected {
                background: $level-selected-color;
            }
        }

        &.criterium-total {
            height: 1.58em;
            min-width: 3.5em;
        }

        &.score-number-calc.mod-criterium {
            background: $score-lighter;
            line-height: 1.6em;
            margin-bottom: -1px;
            padding-top: 1px;
        }
    }

    .levels-table-header, .rubric-table, .app-tool-item {
        transition: opacity 200ms;

        &.is-demo-inactive {
            max-height: 1px;
            opacity: 0;
            pointer-events: none;
        }
    }

    .btn-score-number {
        cursor: pointer;
        outline: none;

        &:hover, &:focus {
            border: 1px solid $level-selected-color;
        }

        &.is-selected {
            &:hover, &:focus {
                box-shadow: inset 0 0 0 1px white;
            }
        }
    }

    .criterium-level-title, .score-number {
        &.is-selected {
            color: #fff;
        }
    }

    .score-entry-view {
        margin-left: 1em;
        width: 3.5em;
    }

    .default-feedback-entry-view {
        display: none;
        line-height: 1.4em;
        padding: .3em .5em;

        &.is-feedback-visible {
            display: block;
        }
    }

    .custom-feedback {
        display: none;
        margin-bottom: 1em;
        margin-left: 20em;

        &.is-feedback-visible {
            display: block;
        }
    }

    .ta-custom-feedback {
        border: 1px solid #d0d0d0;
        border-radius: $border-radius;
        height: 2.2em;
        max-width: 100%;
        padding: .2em .4em 0;
        resize: none;
        width: 40em;

        &::placeholder {
            color: #aaa;
            opacity: 1;
        }

        &:hover {
            border: 1px solid #aaa;
        }

        &:focus {
            border: 1px solid $input-color-focus;
        }

        &:hover, &:focus {
            outline: none;
            resize: both;

            &::placeholder {
                color: #666;
            }
        }
    }

    .rubric-entry-error {
        border-bottom: 2px solid red;
        color: red;
        margin-left: 19.8em;
        margin-right: 4.5em;
        padding: 0 .25em;
    }

    @media only screen and (max-width: 899px) {
        .rubric-entry-view {
            max-width: 100%;
            width: 40em;
        }

        .mod-entry-view {
            &.levels-table-header {
                display: none;
            }

            &.criterium-level {
                margin-left: .8em;
            }

            &.criterium-level-header {
                align-content: center;
                display: flex;
                justify-content: center;
                justify-items: center;
                margin-top: .5em;
                max-width: 40em;
                padding: 0 .25em;
                text-align: left;
            }

            &.subtotal {
                margin-right: .5em;
                max-width: 41.25em;
            }

            &.criterium-total {
                display: none;
            }
        }

        .default-feedback-entry-view {
            max-width: 40em;
        }

        .custom-feedback {
            margin: -1.5em 1em 1em 1.3em;
        }
    }

    @media only screen and (min-width: 900px) {
        .mod-entry-view {
            .criterium {
                align-items: baseline;
            }

            .criterium-level:nth-last-child(2) {
                margin-right: 1em;
            }
        }
    }
</style>
