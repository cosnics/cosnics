<template>
    <div class="rubric mod-bf" :style="{'--num-cols': rubric.levels.length}">
        <div class="table-header-filler" aria-hidden="true"></div>
        <ul class="rubric-header mod-responsive">
            <li class="rubric-header-title" v-for="level in rubric.levels">{{ level.title }}</li>
        </ul>
        <h1 class="rubric-title">{{ rubric.title }}</h1>
        <template v-for="cluster in rubric.clusters">
            <div class="treenode-title-header mod-responsive">
                <div class="treenode-title-header-pre"></div>
                <h2 class="treenode-title cluster-title">{{ cluster.title }}</h2>
            </div>
            <template v-for="category in cluster.categories">
                <div v-if="category.title && rubric.getAllCriteria(category).length > 0" class="treenode-title-header mod-responsive" :style="`--category-color: ${ category.title && category.color ? category.color : 'transparent' }`">
                    <div class="treenode-title-header-pre mod-category"></div>
                    <h3 class="treenode-title category-title">{{ category.title }}</h3>
                </div>
                <template v-for="{criterium, ext} in getCriteriumRowsData(category)">
                    <div class="treenode-title-header mod-responsive" :style="`--category-color: ${ !(category.title && category.color) ? '#999' : category.color }`">
                        <div class="treenode-title-header-pre mod-criterium"></div>
                        <h4 class="treenode-title criterium-title">{{ criterium.title }}</h4>
                    </div>
                    <div class="treenode-rubric-input">
                        <div class="treenode-choices">
                            <div class="treenode-choice" v-for="choice in ext.choices">
                                <div class="treenode-level mod-bf">
                                    <span class="treenode-level-title">{{ choice.level.title }}</span>
                                    <span v-if="rubric.useScores" :aria-label="`${ choice.score } ${ $t('points') }`">{{ choice.score }}</span>
                                </div>
                                <div class="treenode-level-description-input" @click="focusTextField">
                                    <feedback-field :choice="choice.choice" @input="updateHeight" @change="updateFeedback(choice.choice, criterium, choice.level)"></feedback-field>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </template>
            <div class="cluster-sep mod-bf"></div>
        </template>
        <!--<ul class="clusters mod-builder-full-view">
            <li v-for="cluster in rubric.clusters" class="cluster-list-item" v-if="rubric.getAllCriteria(cluster).length > 0">
                <div class="cluster">
                    <h2 class="cluster-title mod-builder-full-view">{{ cluster.title }}</h2>
                    <ul class="categories">
                        <li v-for="category in cluster.categories" class="category-list-item" :style="`--category-color: ${ category.title && category.color ? category.color : 'transparent' }`" v-if="rubric.getAllCriteria(category).length > 0">
                            <div class="category">
                                <h3 v-if="category.title" class="category-title category-indicator mod-builder-full-view">{{ category.title }}</h3>
                                <ul class="criteria" :style="`--category-color: ${ !(category.title && category.color) ? '#999' : '' }`">
                                    <li v-for="{criterium, ext} in getCriteriumRowsData(category)" class="criterium-list-item">
                                        <div class="criterium mod-responsive mod-builder-full-view">
                                            <div class="criterium-title-header mod-responsive mod-builder-full-view">
                                                <h4 class="criterium-title category-indicator mod-builder-full-view">{{ criterium.title }}</h4>
                                            </div>
                                            <ul class="criterium-levels mod-builder-full-view">
                                                <li v-for="data in ext.choices" class="criterium-level mod-builder-full-view">
                                                    <div class="criterium-level-header mod-builder-full-view" :class="{ 'is-using-scores': rubric.useScores }">
                                                        <div class="criterium-level-title mod-builder-full-view">
                                                            {{data.level.title}}
                                                        </div>
                                                        <div v-if="rubric.useScores" class="score-number">--><!--<i class="check fa"/>--><!--{{ data.score }}</div>
                                                    </div>
                                                    <div class="default-feedback-full-view" @click="focusTextField">
                                                        <feedback-field :choice="data.choice" @input="updateHeight" @change="updateFeedback(data.choice, criterium, data.level)"></feedback-field>
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
            </li>
        </ul>-->
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue, Watch} from 'vue-property-decorator';
    import Rubric from '../Domain/Rubric';
    import Category from '../Domain/Category';
    import Criterium from '../Domain/Criterium';
    import Level from '../Domain/Level';
    import Choice from '../Domain/Choice';
    import FeedbackField from '../Components/FeedbackField.vue';
    import DataConnector from '../Connector/DataConnector';
    import debounce from 'debounce';

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

        constructor() {
            super();
            this.onResize = debounce(this.onResize, 200);
        }

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

        getCriteriumRowsData(category: Category) {
            return category.criteria.map(criterium => ({
                criterium,
                ext: this.getCriteriumData(criterium)
            }));
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

        onResize() {
            this.updateHeightAll();
        }

        created() {
            if (this.rubric) {
                this.initScores(this.rubric);
                // todo: get rubric data id
            }
        }

        updateHeightAll() {
            this.$nextTick(() => {
                document.querySelectorAll('.ta-default-feedback').forEach(el => {
                    updateHeight(el as HTMLElement);
                });
            });
        }

        destroyed() {
            window.removeEventListener('resize', this.onResize);
        }

        mounted() {
            window.addEventListener('resize', this.onResize);
            this.updateHeightAll();
        }

        @Watch('rubric.useScores')
        onUsesScoresChange() {
            this.updateHeightAll();
        }
    }
</script>

<style lang="scss" scoped>
    .rubric {
        display: grid;
        grid-column-gap: .7rem;
        grid-row-gap: .7rem;
        max-width: max-content;
        padding: 1rem;
        position: relative;

        &.mod-bf {
            grid-template-columns: minmax(20rem, 30rem) minmax(calc(var(--num-cols) * 15rem), calc(var(--num-cols) * 30rem));
        }
    }
    .rubric-title {
        display: none;
    }

    .treenode-level-description-input {
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 3px;
        flex: 1;
        line-height: 1.8rem;
        padding: 0;

        &:hover, &:focus-within {
            border: 1px solid $score-dark;
        }
    }

    .treenode-header.mod-responsive {
        grid-column-start: 1;
    }

    @media only screen and (max-width: 899px) {
        .rubric.mod-bf {
            grid-template-columns: minmax(calc(var(--num-cols) * 5rem), calc(var(--num-cols) * 30rem));
        }
    }

</style>
<style lang="scss" scoped>
    .table-header-filler {
        display: none;
        flex: 1;
        margin-right: .4em;
        max-width: 30rem;
        min-width: 20rem;
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

    .cluster-sep.mod-bf:last-child {
        display: none;
    }

    @media only screen and (min-width: 680px) {
        .treenode-level.mod-bf {
            display: none;
        }

    }

    @media only screen and (max-width: 679px) {
        .default-feedback-full-view {
            margin-left: .75em;
            margin-top: -.25em;
        }
    }

    @media only screen and (min-width: 680px) and (max-width: 899px) {
    }

    @media only screen and (max-width: 899px) {
        .rubric.mod-builder-full-view {
            margin: 0 -.5em;
        }
    }

    @media only screen and (min-width: 900px) {
        .table-header-filler {
            display: block;
        }
    }
</style>
