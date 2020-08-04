<template>
    <div id="app" class="preview-app">
        <div v-if="rubric" class="rubric">
            <link rel="stylesheet"
                  href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
            <div class="rubric-preview">
                <div class="table-header-wrap" aria-hidden="true">
                    <ul class="app-header-tools">
                        <li class="app-header-item"><button class="btn-check" aria-label="Toon standaard feedback beschrijvingen" :aria-expanded="showDefaultFeedbackFields ? 'true' : 'false'" :class="{ checked: showDefaultFeedbackFields }" @click.prevent="toggleDefaultFeedbackFields"><span tabindex="-1"><i class="check fa" aria-hidden="true" />Feedback beschrijvingen</span></button></li>
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
                                            <li v-for="criterium in category.criteria" role="grid" class="criterium-list-item" :class="{'is-feedback-visible': getCriteriumData(criterium).showDefaultFeedback}">
                                                <div class="criterium" role="row">
                                                    <div class="criterium-title-header" role="gridcell">
                                                        <h4 :id="`criterium-${criterium.id}-title`" class="criterium-title category-indicator">{{ criterium.title }}</h4><button v-if="!showDefaultFeedbackFields" class="btn-more" aria-label="Toon standaard feedback beschrijving criterium" :aria-expanded="criterium.showDefaultFeedback ? 'true' : 'false'" @click.prevent="getCriteriumData(criterium).showDefaultFeedback = !getCriteriumData(criterium).showDefaultFeedback"><i tabindex="-1" class="check fa" aria-hidden="true" /></button>
                                                    </div>
                                                    <div v-for="choice in getCriteriumData(criterium).choices" class="criterium-level" role="gridcell" :aria-describedby="`criterium-${criterium.id}-title`">
                                                        <div :aria-checked="choice.level.isDefault" class="criterium-level-header btn-score-number" :class="{ selected: choice.level.isDefault }">
                                                            <div class="criterium-level-title">
                                                                {{choice.title}}
                                                            </div>
                                                            <span class="score-number" :aria-label="`${ choice.score } punten`"><!--<i class="check fa"/>-->{{ choice.score }}</span>
                                                        </div>
                                                        <div class="default-feedback">
                                                            {{ choice.feedback }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric, {RubricJsonObject} from '../Domain/Rubric';
    import Criterium from '../Domain/Criterium';

    interface CriteriumExt {
        criterium: Criterium;
        choices: any[];
        showDefaultFeedback: false;
    }

    @Component({})
    export default class RubricPreview extends Vue {
        private rubric: Rubric|null = null;
        private criteriaData: CriteriumExt[] = [];

        @Prop({type: Object, default: null}) readonly rubricData!: object|null;
        @Prop({type: Object}) readonly uiState!: any;

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

        getCriteriumData(criterium: Criterium) : CriteriumExt {
            const criteriumExt = this.criteriaData.find((_ : CriteriumExt) => _.criterium === criterium);
            if (!criteriumExt) { throw new Error(`No data found for criterium: ${criterium}`); }
            return criteriumExt;
        }

        private initData(rubric: Rubric) {
            rubric.getAllCriteria().forEach(criterium => {
                const criteriumExt: CriteriumExt = { criterium, choices: [], showDefaultFeedback: false };
                rubric.levels.forEach(level => {
                    const choice = rubric.getChoice(criterium, level);
                    const score = rubric.getChoiceScore(criterium, level);
                    criteriumExt.choices.push({ title: level.title, feedback: choice?.feedback || '', score, choice, level});
                });
                this.criteriaData.push(criteriumExt);
            });
        }

        mounted() {
            if (this.rubricData) {
                this.rubric = Rubric.fromJSON(this.rubricData as RubricJsonObject);
                this.initData(this.rubric);
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

    .preview-app {
        &#app {
            color: $text-color;
            border-top: 1px solid #d6d6d6; /** Added this for result view **/
        }

        .rubric-preview {
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
            margin-right: 0;
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
            background: #e3e3e3;
            border-bottom-color: transparent;
            cursor: unset;
            width: 100%;
        }

        .criterium-level-header.selected {
            background: $level-selected-color;
        }

        .btn-score-number {
            outline: none;
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

    @media only screen and (max-width: 899px) {
        .preview-app {
            .rubric-preview {
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
        .preview-app .criterium {
            align-items: baseline;

            .criterium-title {
                margin-right: 1.5em;
            }
        }
        .preview-app .criterium-title-header {
            border-top: 1px solid $score-light;
        }
    }
</style>
