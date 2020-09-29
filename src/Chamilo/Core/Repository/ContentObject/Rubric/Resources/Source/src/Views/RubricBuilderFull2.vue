<template>
    <div class="rubric mod-builder-full-view" :style="{'--num-cols': rubric.levels.length}">
        <div class="table-header-filler" aria-hidden="true"></div>
        <div class="levels-table-header mod-builder-full-view">
            <div v-for="level in rubric.levels" class="level-table-header-title mod-builder-full-view">
                {{level.title}}
            </div>
        </div>
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
                    <ul class="criterium-levels mod-builder-full-view">
                        <li v-for="data in ext.choices" class="criterium-level mod-builder-full-view">
                            <div class="criterium-level-header mod-builder-full-view" :class="{ 'is-using-scores': rubric.useScores }">
                                <div class="criterium-level-title mod-builder-full-view">
                                    {{data.level.title}}
                                </div>
                                <div v-if="rubric.useScores" class="score-number"><!--<i class="check fa"/>-->{{ data.score }}</div>
                            </div>
                            <div class="default-feedback-full-view" @click="focusTextField">
                                <feedback-field :choice="data.choice" @input="updateHeight" @change="updateFeedback(data.choice, criterium, data.level)"></feedback-field>
                            </div>
                        </li>
                    </ul>
                </template>
            </template>
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

        &.mod-builder-full-view {
            grid-template-columns: minmax(20rem, 30rem) minmax(calc(var(--num-cols) * 15rem), calc(var(--num-cols) * 30rem));
        }
    }
    .rubric-title {
        display: none;
    }
    .treenode-header.mod-responsive {
        grid-column-start: 1;
    }
    @media only screen and (min-width: 900px) {
        .levels-table-header.mod-builder-full-view {
            grid-column-start: 2;
        }
    }
    @media only screen and (max-width: 899px) {
        .rubric.mod-builder-full-view {
            grid-template-columns: minmax(calc(var(--num-cols) * 5rem), calc(var(--num-cols) * 30rem));
        }
    }
    @media only screen and (max-width: 679px) {
        .levels-table-header.mod-builder-full-view {
            display: none;
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

    .mod-builder-full-view {

        &.criterium-level {
            display: flex;
            flex-direction: column;
        }

        &.criterium-level-header {
            border-color: transparent;
            cursor: text;
            background: #e0e0e0;
        }

        &.cluster-title, &.category-title {
            max-width: 70ch;
            margin-bottom: .2em;
        }

        &.criterium-title {
            margin-right: .5em;
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

    @media only screen and (min-width: 680px) {
        .mod-builder-full-view {
            &.levels-table-header {
                margin-left: 1.335em;
            }

            &.level-table-header-title {
                max-width: 33rem;
            }

            &.criterium-level-header {
                display: none;

                /*&.is-using-scores {
                    display: block;
                }*/
            }

            &.criterium-level-title {
                height: 1px;
                left: -10000px;
                overflow: hidden;
                position: absolute;
                top: auto;
                width: 1px;
            }

            &.criterium.mod-responsive {
                margin-bottom: 1em;
            }

            &.criterium-level {
                max-width: 33rem;
            }
        }
    }

    @media only screen and (max-width: 679px) {
        .mod-builder-full-view {
            &.rubric {
                margin: 0;
            }

            &.criterium.mod-responsive {
                margin-bottom: 1em;
            }

            &.rubric-table-header {
                display: none;
            }

            &.criterium-level {
                margin-bottom: .5em;
            }

            &.criterium-level-header {
                display: flex;
                margin: .3em 0 -.1em .75em;
                padding-left: .25em;
                padding-right: .3em;
                text-align: left;
            }

            &.criterium-levels {
                margin-left: 0;
            }
        }

        .default-feedback-full-view {
            margin-left: .75em;
            margin-top: -.25em;
        }
    }
    @media only screen and (min-width: 680px) and (max-width: 899px) {
        .clusters.mod-builder-full-view {
            margin-top: 1em;
        }
        .criterium-level.mod-builder-full-view {
            margin-top: .3em;
        }
    }

    @media only screen and (max-width: 899px) {
        .rubric.mod-builder-full-view {
            margin: 0 -.5em;
        }

        .criterium-level-header.mod-builder-full-view {
            margin-top: .25em;
        }
    }

    @media only screen and (min-width: 900px) {
        .table-header-filler {
            display: block;
        }

        .rubric-table-header.mod-builder-full-view {
            display: flex;
        }

        .levels-table-header.mod-builder-full-view {
            /*margin-left: 19.35em;*/
            flex: 1;
            margin-left: 0;

        }

        .criterium-title-header.mod-builder-full-view {
            flex: 1;
            max-width: 30rem;
            min-width: 20rem;
        }
    }
</style>
