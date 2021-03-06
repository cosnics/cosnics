<i18n>
{
    "en": {
        "formatting": "Formatting",
        "points": "points"
    },
    "fr": {
        "formatting": "Mise en forme",
        "points": "points"
    },
    "nl": {
        "formatting": "Opmaakhulp",
        "points": "punten"
    }
}
</i18n>
<template>
    <div class="rubric mod-bf" :style="{'--num-cols': rubric.levels.length}">
        <formatting-help v-if="showFormatting" @close="showFormatting = false" class="mod-bf"></formatting-help>
        <ul class="rubric-tools">
            <li><a href="#" role="button" class="tools-show-formatting" @click.prevent="showFormatting=!showFormatting">{{ $t('formatting') }}</a></li>
        </ul>
        <ul class="rubric-header mod-responsive">
            <li class="rubric-header-title" v-for="level in rubric.levels">{{ level.title }}</li>
        </ul>
        <template v-for="cluster in rubric.clusters">
            <div class="treenode-title-header mod-responsive">
                <div class="treenode-title-header-pre"></div>
                <h1 class="treenode-title cluster-title">{{ cluster.title }}</h1>
            </div>
            <template v-for="category in cluster.categories">
                <div v-if="category.title && rubric.getAllCriteria(category).length > 0" class="treenode-title-header mod-responsive" :style="`--category-color: ${ category.title && category.color ? category.color : 'transparent' }`">
                    <div class="treenode-title-header-pre mod-category"></div>
                    <h2 class="treenode-title category-title">{{ category.title }}</h2>
                </div>
                <template v-for="{criterium, ext} in getCriteriumRowsData(category)">
                    <div class="treenode-title-header mod-responsive mod-bf" :style="`--category-color: ${ !(category.title && category.color) ? '#999' : category.color }`">
                        <div class="treenode-title-header-pre mod-criterium"></div>
                        <h3 class="treenode-title criterium-title u-markdown-criterium" v-html="criterium.toMarkdown()"></h3>
                    </div>
                    <div class="treenode-rubric-input">
                        <div class="treenode-choices">
                            <div class="treenode-choice" v-for="choice in ext.choices">
                                <div class="treenode-level mod-bf">
                                    <span class="treenode-level-title">{{ choice.level.title }}</span>
                                    <span v-if="useScores" :aria-label="`${ choice.score } ${ $t('points') }`">{{ choice.score }}</span>
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
    import FormattingHelp from '../Components/FormattingHelp.vue';
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
            FeedbackField, FormattingHelp
        },
    })
    export default class RubricBuilderFull extends Vue {
        @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
        @Prop(DataConnector) readonly dataConnector!: DataConnector|null;
        private criteriaData: CriteriumExt[] = [];
        private showFormatting = false;

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

        get useScores() {
            return this.rubric.useScores;
        }

        get useGrades() {
            return !this.rubric.useScores;
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

<style lang="scss">
    .rubric.mod-bf {
        grid-template-columns: minmax(20rem, 30rem) minmax(calc(var(--num-cols) * 15rem), calc(var(--num-cols) * 30rem));
    }

    .formatting-help.mod-bf {
        background: #fff;
        border: 1px solid #aaa;
        margin-right: 1rem;
        padding-right: 1rem;
        padding-top: 1rem;
        position: absolute;
        right: 0;
        top: 0;
        width: 30rem;
        z-index: 1000;
    }

    .tools-show-formatting {
        &, &:hover, &:active, &:focus {
            outline: none;
            text-decoration: none;
        }

        &:focus {
            outline: 1px solid $input-color-focus;
            outline-offset: 2px;
        }
    }

    .treenode-level-description-input {
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: $border-radius;
        flex: 1;
        line-height: 1.8rem;
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

    @media only screen and (min-width: 900px) {
        .treenode-title-header.mod-bf {
            padding-top: .6rem;
        }
    }

    @media only screen and (max-width: 899px) {
        .rubric.mod-bf {
            grid-template-columns: minmax(calc(var(--num-cols) * 5rem), calc(var(--num-cols) * 30rem));
        }
    }

    @media only screen and (min-width: 680px) {
        .treenode-level.mod-bf {
            display: none;
        }
    }
</style>
