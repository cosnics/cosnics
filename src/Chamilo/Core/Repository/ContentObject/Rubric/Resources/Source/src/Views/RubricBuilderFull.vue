<template>
    <div class="rubric mod-builder-full-view">
        <div class="rubric-table-header mod-builder-full-view">
            <div class="levels-table-header mod-builder-full-view">
                <div v-for="level in rubric.levels" class="level-table-header-title">
                    {{level.title}}
                </div>
            </div>
        </div>
        <h1 class="rubric-title">{{ rubric.title }}</h1>
        <ul class="clusters">
            <li v-for="cluster in rubric.clusters" class="cluster-list-item">
                <div class="cluster">
                    <h2 class="cluster-title">{{ cluster.title }}</h2>
                    <ul class="categories">
                        <li v-for="category in cluster.categories" class="category-list-item" :style="`--category-color: ${ category.title ? (category.color || '#999') : '#999' }`">
                            <div class="category">
                                <h3 v-if="category.title" class="category-title category-indicator">{{ category.title }}</h3>
                                <ul class="criteria">
                                    <li v-for="criterium in category.criteria" class="criterium-list-item">
                                        <div class="criterium mod-responsive">
                                            <div class="criterium-title-header mod-responsive">
                                                <h4 class="criterium-title category-indicator">{{ criterium.title }}</h4>
                                            </div>
                                            <div v-for="data in getCriteriumData(criterium).choices" class="criterium-level mod-builder-full-view">
                                                <div class="criterium-level-header mod-builder-full-view">
                                                    <div class="criterium-level-title">
                                                        {{data.level.title}}
                                                    </div>
                                                    <div class="score-number"><!--<i class="check fa"/>-->{{ data.score }}</div>
                                                </div>
                                                <div class="default-feedback-full-view" @click="focusTextField">
                                                    <feedback-field :choice="data.choice" @input="updateHeight" @change="updateFeedback(data.choice, criterium, data.level)"></feedback-field>
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
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric from '../Domain/Rubric';
    import Criterium from '../Domain/Criterium';
    import Level from '../Domain/Level';
    import Choice from '../Domain/Choice';
    import FeedbackField from '../Components/FeedbackField.vue';
    import DataConnector from '../Connector/DataConnector';

    function updateHeight(elem: HTMLElement) {
        elem.style.height = '';
        elem.style.height = `${elem.scrollHeight}px`;
    }

    interface CriteriumExt {
        criterium: Criterium;
        choices: any[];
    }

    @Component({
        components: {
            FeedbackField
        },
    })
    export default class RubricBuilderFull extends Vue {
        @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
        @Prop(DataConnector) readonly dataConnector!: DataConnector|null;
        private criteriaData: CriteriumExt[] = [];

        updateHeight(e: InputEvent) {
            this.$nextTick(() => {
                updateHeight(e.target as HTMLElement);
            });
        }

        updateFeedback(choice: Choice, criterium: Criterium, level: Level) {
            this.dataConnector?.updateChoice(choice, criterium, level);
        }

        focusTextField(elem: any) {
            if (elem.target.className === 'default-feedback') {
                elem.target.querySelector('.ta-default-feedback').focus();
            }
        }

        getCriteriumData(criterium: Criterium) : CriteriumExt {
            const criteriumExt = this.criteriaData.find((_ : CriteriumExt) => _.criterium === criterium);
            if (!criteriumExt) { throw new Error(`No data found for criterium: ${criterium}`); }
            return criteriumExt;
        }

        private initScores(rubric: Rubric) {
            rubric.getAllCriteria().forEach(criterium => {
                const criteriumExt: CriteriumExt = { criterium: criterium, choices: [] };
                rubric.levels.forEach(level => {
                    const choice = rubric.getChoice(criterium, level);
                    const score = rubric.getChoiceScore(criterium, level);
                    criteriumExt.choices.push({ level, choice, score});
                });
                this.criteriaData.push(criteriumExt);
            });
        }

        created() {
            if (this.rubric) {
                this.initScores(this.rubric);
                // todo: get rubric data id
            }
        }

        mounted() {
            this.$nextTick(() => {
                document.querySelectorAll('.ta-default-feedback').forEach(el => {
                    updateHeight(el as HTMLElement);
                });
            });
        }
    }
</script>
<style lang="scss">
    .mod-builder-full-view {
        &.levels-table-header {
            margin-left: 19.8em;
        }

        &.criterium-level {
            display: flex;
            flex-direction: column;
        }

        &.criterium-level-header {
            border-color: transparent;
            cursor: text;
        }
    }

    .default-feedback-full-view {
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 3px;
        flex: 1;
        line-height: 1.4em;
        padding: 0;

        &:hover, &:focus-within {
            border: 1px solid $score-dark;
        }
    }

    .ta-default-feedback {
        /*background: transparent;*/
        border: none;
        overflow: hidden;
        padding: .3em;
        resize: none;
        width: 100%;

        &:focus {
            outline: none;
        }
    }

    @media only screen and (max-width: 899px) {
        .mod-builder-full-view {
            &.rubric {
                margin: 0;
            }

            &.rubric-table-header {
                display: none;
            }

            &.criterium-level {
                margin-bottom: .5em;
            }

            &.criterium-level-header {
                background: #ddd;
                display: flex;
                margin: .3em 0 -.1em .75em;
                padding-left: .25em;
                padding-right: .3em;
                text-align: left;
            }
        }

        .default-feedback-full-view {
            margin-left: .75em;
            margin-top: -.25em;
        }
    }
</style>
