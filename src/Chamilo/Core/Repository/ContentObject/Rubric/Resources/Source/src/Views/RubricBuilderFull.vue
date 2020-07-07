<template>
    <div class="rubric">
        <div class="rubric-builder-full-view">
            <div class="table-header-wrap">
                <div class="table-header">
                    <div v-for="level in rubric.levels" class="table-header-title">
                        {{level.title}}
                    </div>
                </div>
            </div>
            <h1 class="rubric-title">{{ rubric.title }}</h1>
            <ul class="clusters" :class="{'show-default-feedback': true }">
                <li v-for="cluster in rubric.clusters" class="cluster-list-item">
                    <div class="cluster">
                        <h2 class="cluster-title">{{ cluster.title }}</h2>
                        <ul class="categories">
                            <li v-for="category in cluster.categories" class="category-list-item" :style="`--category-color: ${ category.title ? (category.color || '#999') : '#999' }`">
                                <div class="category">
                                    <h3 v-if="category.title" class="category-title category-indicator">{{ category.title }}</h3>
                                    <ul class="criteria">
                                        <li v-for="criterium in category.criteria" class="criterium-list-item">
                                            <div class="criterium">
                                                <div class="criterium-title-header">
                                                    <h4 class="criterium-title category-indicator">{{ criterium.title }}</h4>
                                                </div>
                                                <div v-for="level in getCriteriumData(criterium).choices" class="criterium-level">
                                                    <div class="criterium-level-header">
                                                        <div class="criterium-level-title">
                                                            {{level.title}}
                                                        </div>
                                                        <div class="score-number"><!--<i class="check fa"/>-->{{ level.score }}</div>
                                                    </div>
                                                    <div class="default-feedback" @click="focusTextField">
                                                        <feedback-field :choice="level.choice" @input="updateHeight" @change="updateFeedback(level.choice)"></feedback-field>
                                                        <!--<textarea v-model="level.feedback" class="ta-feedback" @input="updateFeedback"></textarea>-->
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
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric from '../Domain/Rubric';
    import Criterium from '../Domain/Criterium';
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

        updateFeedback(choice: Choice) {
            this.dataConnector?.updateChoice(choice);
        }

        focusTextField(elem: any) {
            if (elem.target.className === 'default-feedback') {
                elem.target.querySelector('.ta-feedback').focus();
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
                    criteriumExt.choices.push({ title: level.title, choice, score});
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
                document.querySelectorAll('.ta-feedback').forEach(el => {
                    updateHeight(el as HTMLElement);
                });
            });
        }
    }
</script>
<style lang="scss">
    .builder-full-app {
        &#app {
            color: $text-color;
        }

        .table-header {
            margin-left: 19.8em;

            .table-header-title {
                flex: 1;
            }
        }

        .criterium-title-header {
            width: 19em;
            min-width: 19em;
        }

        .criterium .criterium-level {
            display: flex;
            flex-direction: column;
        }

        .criterium-level-header {
            cursor: text;
            border-color: transparent;
        }

        .default-feedback {
            padding: 0;
            flex: 1;
            border: 1px solid #ccc;
            border-radius: 3px;
            background-color: white;

            &:hover, &:focus-within {
                border: 1px solid $score-dark;
            }
        }

        .ta-feedback {
            padding: .3em;
            width: 100%;
            /*background: transparent;*/
            border: none;
            resize: none;
            overflow: hidden;

            &:focus {
                outline: none;
            }
        }
    }

    @media only screen and (max-width: 899px) {
        .builder-full-app {
            .rubric {
                margin: 0;
            }

            .table-header-wrap {
                display: none;
            }

            .criterium {
                flex-direction: column;
                margin-bottom: 2em;
            }

            .criterium-level-title {
                color: darken($title-color, 5%);
            }

            .criterium-level {
               margin-bottom: .5em;
            }

            .criterium-level-header {
                background: #ddd;
                display: flex;
                margin: .3em 0 -.1em .75em;
                padding-left: .25em;
                padding-right: .3em;
                text-align: left;
            }
            
            .default-feedback {
                margin-top: -.25em;
                margin-left: .75em;
            }
        }
    }
</style>