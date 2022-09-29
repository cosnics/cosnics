<i18n>
{
    "en": {
        "formatting": "Formatting",
        "weight": "Weight"
    },
    "fr": {
        "formatting": "Mise en forme",
        "weight": "Poids"
    },
    "nl": {
        "formatting": "Opmaakhulp",
        "weight": "Gewicht"
    }
}
</i18n>
<template>
    <div class="rubric mod-bf" :class="{'mod-weight': rubric.useWeights}" :style="{'--num-cols': rubric.maxNumLevels}">
        <formatting-help v-if="showFormatting" @close="showFormatting = false" class="mod-bf"></formatting-help>
        <ul class="rubric-tools">
            <li><a href="#" role="button" class="tools-show-formatting" @click.prevent="showFormatting=!showFormatting">{{ $t('formatting') }}</a></li>
        </ul>
        <div v-if="rubric.useWeights" class="treenode-weight-header mod-show">
            <span>{{ $t('weight') }}</span>
        </div>
        <div class="rubric-header mod-show" v-if="!rubric.hasCustomLevels">
            <div class="rubric-header-title" v-for="level in rubric.rubricLevels">{{ level.title }}</div>
        </div>
        <template v-for="(cluster, index) in rubric.clusters">
            <div class="treenode-title-header rb-lg:col-start-1">
                <h1 class="treenode-title cluster-title">{{ cluster.title }}</h1>
            </div>
            <template v-for="category in cluster.categories">
                <div v-if="category.title && rubric.getAllCriteria(category).length > 0" class="treenode-title-header mod-category has-category rb-lg:col-start-1" :style="`--category-color: ${ category.title && category.color ? category.color : '#999' }`">
                    <div class="treenode-title-header-pre mod-category" :class="{'mod-no-color': !category.color}"></div>
                    <h2 class="treenode-title category-title">{{ category.title }}</h2>
                </div>
                <template v-for="{criterium, ext} in getCriteriumRowsData(category)">
                    <div class="treenode-title-header rb-lg:col-start-1" :class="{'has-category': !!category.title}" :style="`--category-color: ${ !(category.title && category.color) ? '#999' : category.color }`">
                        <div class="treenode-title-header-pre mod-criterium"></div>
                        <h3 class="treenode-title criterium-title u-markdown-criterium" :class="{'mod-no-category': !category.title}" v-html="criterium.toMarkdown()"></h3>
                    </div>
                    <div v-if="rubric.useWeights" class="treenode-weight mod-pad rb-md:col-span-full">
                        <span class="treenode-weight-title">{{ $t('weight') }}: </span>
                        <input v-if="rubric.useRelativeWeights" type="number" :placeholder="rubric.eqRestWeight.toLocaleString()" v-model.number="criterium.rel_weight" class="input-detail rel-weight" :class="{'is-set': criterium.rel_weight !== null, 'is-error': rubric.eqRestWeight < 0}" @input="onWeightChange($event, criterium)" min="0" max="100" />
                        <template v-else>
                            <span v-if="rubric.filterLevelsByCriterium(criterium).length" style="margin-left: 1em">100</span>
                            <input v-else type="number" v-model.number="criterium.weight" class="input-detail abs-weight" @input="onWeightChange($event, criterium)" min="0" max="100" />
                        </template>
                        <span class="sr-only">%</span><i class="fa fa-percent" aria-hidden="true"></i>
                    </div>
                    <tree-node-descriptions :rubric="rubric" :criterium="criterium" :ext="ext"
                                            @input="updateHeight"
                                            @update-level-description="updateLevelDescription"
                                            @update-choice-feedback="updateFeedback"></tree-node-descriptions>
                </template>
            </template>
            <div class="cluster-sep" v-if="index < rubric.clusters.length - 1"></div>
        </template>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue, Watch} from 'vue-property-decorator';
    import {TreeNodeExt} from '../Util/interfaces';
    import Rubric from '../Domain/Rubric';
    import Category from '../Domain/Category';
    import Criterium from '../Domain/Criterium';
    import Level from '../Domain/Level';
    import Choice from '../Domain/Choice';
    import DescriptionField from '../Components/Builder/FullView/DescriptionField.vue';
    import FormattingHelp from '../Components/Builder/FormattingHelp.vue';
    import DataConnector from '../Connector/DataConnector';
    import TreeNodeDescriptions from '../Components/Builder/FullView/TreeNodeDescriptions.vue';
    import debounce from 'debounce';

    function updateHeight(elem: HTMLElement) {
        elem.style.height = '';
        elem.style.height = `${elem.scrollHeight + 14}px`;
    }

    @Component({
        components: {
            DescriptionField, FormattingHelp, TreeNodeDescriptions
        },
        filters: {
            formatNum: function (v: number) {
                return v.toLocaleString(undefined, {maximumFractionDigits: 2});
            }
        }
    })
    export default class RubricBuilderFull extends Vue {
        @Prop({type: Rubric, required: true}) readonly rubric!: Rubric;
        @Prop(DataConnector) readonly dataConnector!: DataConnector|null;
        private criteriaData: TreeNodeExt[] = [];
        private showFormatting = false;

        constructor() {
            super();
            this.onResize = debounce(this.onResize, 200);
            this.onWeightChange = debounce(this.onWeightChange, 750);
        }

        updateHeight(e: InputEvent) {
            this.$nextTick(() => {
                updateHeight(e.target as HTMLElement);
            });
        }

        updateLevelDescription(level: Level) {
            this.dataConnector?.updateLevel(level);
        }

        updateFeedback(choice: Choice, criterium: Criterium, level: Level) {
            this.dataConnector?.updateChoice(choice, criterium, level);
        }

        focusTextField(elem: any) {
            if (elem.target.className === 'default-feedback') {
                elem.target.querySelector('.ta-default-feedback').focus();
            }
        }

        onWeightChange(event: InputEvent, criterium: Criterium) {
            const el = event.target as HTMLInputElement;
            if (!el.checkValidity()) {
                el.reportValidity();
                return;
            }
            if (this.rubric.useRelativeWeights && typeof criterium.rel_weight !== 'number') {
                criterium.rel_weight = null;
            } else if (!this.rubric.useRelativeWeights) {
                const criteriumExt = this.getCriteriumData(criterium);
                criteriumExt.choices = this.getCriteriumChoices(criterium);
            }
            this.dataConnector?.updateTreeNode(criterium);
        }

        getCriteriumChoices(criterium: Criterium) {
            const rubric = this.rubric;

            return rubric.rubricLevels.map(level => ({
                level,
                choice: rubric.getChoice(criterium, level),
                score: rubric.getChoiceScore(criterium, level)
            }));
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

        getCriteriumData(criterium: Criterium) : TreeNodeExt {
            const criteriumExt = this.criteriaData.find((_ : TreeNodeExt) => _.treeNode === criterium);
            if (!criteriumExt) { throw new Error(`No data found for criterium: ${criterium}`); }
            return criteriumExt;
        }

        private initScores(rubric: Rubric) {
            rubric.getAllCriteria().forEach(criterium => {
                const levels = rubric.filterLevelsByCriterium(criterium);
                const choices = levels.length ? [] : this.getCriteriumChoices(criterium);
                this.criteriaData.push({
                    treeNode: criterium, levels, choices, showDefaultFeedback: false
                });
            });
        }

        getChoiceScore(choice: any): number {
            return this.rubric.useRelativeWeights ? choice.level.score : choice.score;
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
    .input-detail {
        background-color: #f7fcfc;
        border: 1px solid #d4d4d4;
        border-radius: 3px;
        padding: 2px 5px;
    }

    .input-detail.abs-weight {
        text-align: right;
    }

    .input-detail.abs-weight, .input-detail.rel-weight, .input-detail.mod-range {
        width: 3.5em;

        -moz-appearance: textfield;
        &::-webkit-outer-spin-button,
        &::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        &.is-set:not(:focus) {
            background-color: #d3eee0;
            color: #4a4a4a;
        }

        &:focus {
            border-color: #66afe9;
            outline: 0;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.08), 0 0 8px rgba(102, 175, 233, 0.6);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.08), 0 0 8px rgba(102, 175, 233, 0.6)
            /*background-color: #edf8f2;*/
        }

        &:focus::placeholder {
            opacity: 0.5;
        }

        &.is-error {
            border-color: #ff8080;
        }

        &.is-error::placeholder {
            color: #c94949;
        }

        &.is-set.is-error {
            background-color: #feecea;
        }

        + .fa-percent {
            color: rgb(129, 169, 177);
            font-size: 1rem;
            margin: 0 .15rem;
        }
    }

    .rubric.mod-bf {
        grid-template-columns: minmax(max-content, 23rem) minmax(calc(var(--num-cols) * 15rem), calc(var(--num-cols) * 30rem));
    }

    .rubric.mod-bf.mod-weight {
        grid-template-columns: minmax(max-content, 16.5rem) 6.7rem minmax(calc(var(--num-cols) * 15rem), calc(var(--num-cols) * 30rem));
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
        background-color: #fafafa;
        border: 1px solid #d4d4d4;
        border-radius: $border-radius;
        flex: 1;
        line-height: 1.8rem;
        padding: 0;

        &:hover {
            background-color: #fff;
        }

        &:focus-within {
            background-color: #fff;
            border: 1px solid #66afe9;
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .08), 0 0 8px rgba(102, 175, 233, .6);
        }
    }

    .ta-default-feedback {
        background: transparent;
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
        .rubric.mod-bf, .rubric.mod-bf.mod-weight {
            grid-template-columns: minmax(calc(var(--num-cols) * 5rem), calc(var(--num-cols) * 30rem));
        }
    }
</style>

<style scoped>
    .treenode-level-description-input.mod-abs-weights >>> .feedback-markup-preview {
        overflow: hidden;
    }
</style>

<style lang="scss" scoped>
    .treenode-weight-header > span {
        padding: 0.7rem 0;
        text-align: left;
    }

    .treenode-title-header {
        position: relative;
    }

    @media only screen and (min-width: 900px) {
        .treenode-title-header {
            padding-top: .6rem;
        }
    }

    .cluster-sep {
        border-color: #deebee;
        margin: 1rem 0 1.5rem;
    }
</style>