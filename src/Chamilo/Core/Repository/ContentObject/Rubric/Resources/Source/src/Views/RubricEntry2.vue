<i18n>
{
    "en": {
        "expand-all": "Expand all",
        "extra-feedback": "Enter extra feedback",
        "feedback": "Feedback",
        "points": "points",
        "rubric": "Rubric",
        "show-default-descriptions": "Show all level descriptions and feedback",
        "show-default-description": "Show level descriptions and feedback",
        "subsection": "Subsection",
        "total": "Total"
    },
    "fr": {
        "expand-all": "Agrandir tout",
        "extra-feedback": "Feed-back supplémentaire",
        "feedback": "Feed-back",
        "points": "points",
        "rubric": "Rubrique",
        "show-default-descriptions": "Afficher toutes descriptions de niveau et feed-back",
        "show-default-description": "Afficher descriptions de niveau et feed-back",
        "subsection": "Sous-section",
        "total": "Total"
    },
    "nl": {
        "expand-all": "Alles uitklappen",
        "extra-feedback": "Geef bijkomende feedback",
        "feedback": "Feedback",
        "points": "punten",
        "rubric": "Rubric",
        "show-default-descriptions": "Toon alle niveauomschrijvingen en feedback",
        "show-default-description": "Toon niveauomschrijvingen en feedback",
        "subsection": "Onderdeel",
        "total": "Totaal"
    }
}
</i18n>

<template>
    <div id="app" :class="{ 'mod-sep': this.options.isDemo || this.options.isPreviewDemo }">
        <link rel="stylesheet"
              href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <div class="rubric" :class="[rubric.useScores ? 'mod-scores' : 'mod-grades']" :style="{'--num-cols': rubric.levels.length}">
            <ul class="app-header-tools mod-entry-view" :class="{ 'mod-demo': this.options.isDemo }">
                <slot name="demoEvaluator"></slot>
                <li class="app-tool-item" :class="{ 'is-demo-inactive': this.options.isDemo && !this.options.evaluator }"><button class="btn-check" :aria-label="$t('show-default-descriptions')" :aria-expanded="showDefaultFeedbackFields ? 'true' : 'false'" :class="{ checked: showDefaultFeedbackFields }" @click.prevent="toggleDefaultFeedbackFields"><span class="lbl-check" tabindex="-1"><i class="btn-icon-check fa" aria-hidden="true" />{{ options.isDemo ? $t('feedback') : $t('expand-all') }}</span></button></li>
            </ul>
            <div class="levels-table-header mod-entry-view" :class="{ 'is-demo-inactive': this.options.isDemo && !this.options.evaluator, 'is-using-scores': rubric.useScores }">
                <div v-for="level in rubric.levels" class="level-table-header-title mod-entry-view">
                    {{ level.title }}
                </div>
            </div>
            <div class="rubric-header-fill"></div>
            <template v-for="{cluster, ext, evaluation, score} in getClusterRowsData(rubric)">
                <div class="cluster-header treenode-header mod-responsive">
                    <h2 class="cluster-title mod-entry-view">{{ cluster.title }}</h2>
                    <button v-if="!preview && !showDefaultFeedbackFields" class="btn-show-feedback mod-cluster" :aria-label="$t('show-default-description')" @click.prevent="ext.showDefaultFeedback = !ext.showDefaultFeedback">
                        <i tabindex="-1" class="btn-icon-show-feedback fa" :class="{'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback}" aria-hidden="true" />
                    </button>
                </div>
                <div v-if="!preview && evaluation !== null" class="custom-feedback mod-cluster" :class="[{ 'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback }]">
                    <textarea class="ta-custom-feedback" :placeholder="$t('extra-feedback')" v-model="evaluation.feedback" @input="onTreeNodeFeedbackChanged(evaluation)"></textarea>
                </div>
                <template v-for="{category, ext, evaluation} in getCategoryRowsData(cluster)">
                    <div class="category-header treenode-header mod-responsive">
                        <h3 class="category-title mod-entry-view category-indicator">{{ category.title }}</h3>
                        <button v-if="!preview && !showDefaultFeedbackFields" class="btn-show-feedback mod-category" :aria-label="$t('show-default-description')" @click.prevent="ext.showDefaultFeedback = !ext.showDefaultFeedback">
                            <i tabindex="-1" class="btn-icon-show-feedback fa" :class="{'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback}" aria-hidden="true" />
                        </button>
                    </div>
                    <div v-if="category.title" class="category-row treenode-hover mod-entry-view">
                        <div v-if="!preview && evaluation !== null" class="custom-feedback mod-category" :class="[{ 'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback }]">
                            <textarea class="ta-custom-feedback" :placeholder="$t('extra-feedback')" v-model="evaluation.feedback" @input="onTreeNodeFeedbackChanged(evaluation)"></textarea>
                        </div>
                    </div>
                    <template v-for="{criterium, ext, evaluation, score} in getCriteriumRowsData(category)">
                        <div class="criterium-header treenode-header mod-responsive mod-entry-view" role="gridcell">
                            <h4 :id="`criterium-${criterium.id}-title`" class="criterium-title mod-entry-view category-indicator">{{ criterium.title }}</h4>
                            <button v-if="!showDefaultFeedbackFields" class="btn-show-feedback mod-criterium" :aria-label="$t('show-default-description')" :title="$t('show-default-description')" :aria-expanded="ext.showDefaultFeedback ? 'true' : 'false'" @click.prevent="ext.showDefaultFeedback = !ext.showDefaultFeedback">
                                <i tabindex="-1" class="btn-icon-show-feedback fa" :class="{'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback}" aria-hidden="true" />
                            </button>
                        </div>
                        <div class="criterium-levels-wrapper">
                            <div v-if="showErrors && !preview && !hasSelection()" class="rubric-entry-error">{{ $t('select-level') }}</div>
                            <ul class="criterium-levels">
                                <li v-for="{choice, isSelected} in getChoicesColumnData(ext, evaluation)" class="criterium-level mod-entry-view" :class="{'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback}" role="gridcell" :aria-describedby="`criterium-${criterium.id}-title`">
                                    <div v-if="preview" :aria-checked="choice.level.isDefault" class="criterium-level-header mod-entry-view" :class="{ 'is-selected': isSelected }">
                                        <div class="criterium-level-title mod-entry-view" :class="{ 'is-selected': isSelected }">
                                            {{choice.title}}
                                        </div>
                                        <span v-if="rubric.useScores" class="score-number" :class="{ 'is-selected': isSelected }" :aria-label="`${ choice.score } ${ $t('points') }`"><!--<i class="check fa"/>-->{{ choice.score }}</span>
                                        <span v-else class="graded-level" :class="{ 'is-selected': isSelected }"><i class="level-icon-check fa fa-check" :class="{ 'is-selected': isSelected }" /></span>
                                    </div>
                                    <button v-else role="radio" :aria-checked="isSelected" class="criterium-level-header mod-entry-view btn-score-number" :class="{ 'is-selected': isSelected }" @click="selectLevel(evaluation, choice.level)">
                                        <div class="criterium-level-title mod-entry-view" :class="{ 'is-selected': isSelected }">
                                            {{choice.title}}
                                        </div>
                                        <span v-if="rubric.useScores" class="score-number" :class="{ 'is-selected': isSelected }" :aria-label="`${ choice.score } ${ $t('points') }`"><!--<i class="check fa"/>-->{{ choice.score }}</span>
                                        <span v-else class="graded-level" :class="{ 'is-selected': isSelected }"><i class="level-icon-check fa fa-check" :class="{ 'is-selected': isSelected }" /></span>
                                    </button>
                                    <div v-if="choice.feedback" class="default-feedback-entry-view" :class="{'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback}" v-html="marked(choice.feedback)"></div>
                                </li>
                                <div v-if="rubric.useScores" class="subtotal criterium-total mod-entry-view" role="gridcell" :aria-describedby="`criterium-${criterium.id}-title`">
                                    <div class="score-number-calc mod-entry-view mod-criterium"><span class="text-hidden">{{ $t('total') }}:</span> {{ preview ? 0 : score }} <span class="text-hidden">{{ $t('points') }}</span></div>
                                </div>
                            </ul>
                            <div v-if="evaluation" class="custom-feedback mod-criterium" :class="[{'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback, 'mod-scores': rubric.useScores, 'mod-default-feedback': hasDefaultFeedback(ext) }]">
                                <textarea class="ta-custom-feedback" :placeholder="$t('extra-feedback')" v-model="evaluation.feedback" @input="onTreeNodeFeedbackChanged(evaluation)"></textarea>
                            </div>
                        </div>
                    </template>
                    <div v-if="rubric.useScores" class="subtotal cluster-total mod-entry-view">
                        <div class="cluster-total-title u-resize">{{ $t('total') }} {{ $t('subsection') }}:</div><div class="score-entry-view u-resize"><div class="score-number-calc mod-cluster">{{ score }} <span class="text-hidden">{{ $t('points') }}</span></div></div>
                    </div>
                </template>
            </template>
            <div v-if="rubric.useScores" class="subtotal rubric-total mod-entry-view">
                <slot name="slot-inner"></slot>
                <div class="rubric-total-title u-resize">{{ $t('total') }} {{ $t('rubric') }}:</div><div class="score-entry-view u-resize"><div class="score-number-calc mod-rubric">{{ getRubricScore() }} <span class="text-hidden">{{ $t('points') }}</span></div></div>
            </div>
            <slot v-else name="slot-inner"></slot>
            <div v-if="rubric.useScores" class="subtotal rubric-total-max mod-entry-view">
                <div class="rubric-total-title u-resize">Maximum:</div><div class="score-entry-view"><div class="score-number-calc mod-rubric-max u-resize">{{ rubric.getMaximumScore() }} <span class="text-hidden">{{ $t('points') }}</span></div></div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Watch, Vue} from 'vue-property-decorator';
    import Rubric from '../Domain/Rubric';
    import TreeNode from '../Domain/TreeNode';
    import Level from '../Domain/Level';
    import Cluster from '../Domain/Cluster';
    import Category from '../Domain/Category';
    import Criterium from '../Domain/Criterium';
    import {TreeNodeEvaluation, TreeNodeExt} from '../Util/interfaces';
    import * as marked from 'marked';
    import DOMPurify from 'dompurify';

    function add(v1: number, v2: number) {
        return v1 + v2;
    }

    @Component({})
    export default class RubricEntry extends Vue {
        private treeNodeData: TreeNodeExt[] = [];
        private offset = 0;

        @Prop({type: Rubric}) readonly rubric!: Rubric;
        @Prop({type: Array, default: () => []}) readonly treeNodeEvaluations!: TreeNodeEvaluation[];
        @Prop({type: Object}) readonly uiState!: any;
        @Prop({type: Object, default: () => ({})}) readonly options!: any;
        @Prop({type: Boolean, default: false}) readonly preview!: boolean;
        @Prop({type: Boolean, default: false}) readonly showErrors!: boolean;

        marked(rawString: string) {
            return DOMPurify.sanitize(marked(rawString));
        }

        calculateOffset() {
            this.$nextTick(() => {
                try {
                    const clustersRect = document.querySelector('.clusters')?.getBoundingClientRect().right || 0;
                    const scoreRect = document.querySelector('.score-number-calc.mod-criterium')?.getBoundingClientRect().right || 0;
                    const offset = clustersRect - scoreRect;
                    this.offset = offset > 0 ? -offset : 0;
                } catch {
                    this.offset = 0;
                }
            });
        }

        hasDefaultFeedback(ext: TreeNodeExt) {
            return ext.choices.map(choice => choice.feedback.length).reduce((v1: number, v2: number) => v1 + v2, 0) > 0;
        }

        getChoicesColumnData(ext: TreeNodeExt, evaluation: TreeNodeEvaluation|null) {
            return ext.choices.map(choice => ({
                choice,
                isSelected: this.preview || !evaluation ? choice.level.isDefault : choice.level === evaluation.level
            }));
        }

        getClusterRowsData(rubric: Rubric) {
            return rubric.clusters
                .filter(cluster => cluster.hasChildren())
                .map(cluster => ({
                    cluster,
                    score: this.getClusterScore(cluster),
                    ...this.getTreeNodeRowData(cluster)
                }));
        }

        getCategoryRowsData(cluster: Cluster) {
            return cluster.categories
                .filter(category => category.hasChildren())
                .map(category => ({
                    category,
                    ...this.getTreeNodeRowData(category)
                }));
        }

        getCriteriumRowsData(category: Category) {
            return category.criteria.map(criterium => ({
                criterium,
                score: this.getCriteriumScore(criterium),
                ...this.getTreeNodeRowData(criterium)
            }));
        }

        getTreeNodeRowData(treeNode: TreeNode) {
            return {
                ext: this.getTreeNodeData(treeNode),
                evaluation: this.getTreeNodeEvaluation(treeNode),
            }
        }

        get showDefaultFeedbackFields() : boolean {
            return this.uiState.showDefaultFeedbackFields;
        }

        toggleDefaultFeedbackFields() {
            const show = this.uiState.showDefaultFeedbackFields = !this.uiState.showDefaultFeedbackFields;
            if (!show) {
                this.rubric.getAllCriteria().forEach(criterium => {
                    this.getTreeNodeData(criterium)!.showDefaultFeedback = false;
                });
            }
        }

        onTreeNodeFeedbackChanged(evaluation: TreeNodeEvaluation) : void {
            if (!evaluation) { return; }
            this.$emit('criterium-feedback-changed', evaluation.treeNode, evaluation.feedback);
        }

        isSelected(criterium: Criterium, level: Level) {
            const isDefaultLevel = level.isDefault;
            if (!this.treeNodeEvaluations) { return isDefaultLevel; }
            const evaluation = this.treeNodeEvaluations.find(evaluation => evaluation.treeNode === criterium);
            if (!evaluation || !evaluation.level) {
                return isDefaultLevel;
            }
            return evaluation.level === level;
        }

        selectLevel(evaluation: TreeNodeEvaluation, level: Level) : void {
            evaluation.level = level;
            // careful: getChoiceScore will fail
            evaluation.score = this.rubric.getChoiceScore(evaluation.treeNode as Criterium, level);
            this.$emit('level-selected', evaluation.treeNode, level);
        }

        getCriteriumScore(criterium: Criterium) : number {
            if (this.preview) { return 0; }
            const evaluation = this.treeNodeEvaluations.find(evaluation => evaluation.treeNode === criterium);
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

        getTreeNodeData(treeNode: TreeNode) : TreeNodeExt|null {
            return this.treeNodeData.find((_ : TreeNodeExt) => _.treeNode === treeNode) || null;
        }

        getTreeNodeEvaluation(treeNode: TreeNode) : TreeNodeEvaluation|null {
            return this.treeNodeEvaluations.find((_ : TreeNodeEvaluation) => _.treeNode === treeNode) || null;
        }

        private initData() {
            const rubric = this.rubric;
            this.treeNodeData = rubric.getAllTreeNodes().map(treeNode => {
                const choices = treeNode instanceof Criterium ? rubric.levels.map(level => {
                    const choice = rubric.getChoice(treeNode, level);
                    const score = rubric.getChoiceScore(treeNode, level);
                    return { title: level.title, feedback: choice?.feedback || '', score, choice, level};
                }) : [];
                return { treeNode, choices, showDefaultFeedback: false };
            });
        }

        destroyed() {
            window.removeEventListener('resize', this.calculateOffset);
        }

        mounted() {
            this.calculateOffset();
            window.addEventListener('resize', this.calculateOffset);
        }

        created() {
            this.initData();
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

        &.mod-scores {
            grid-template-columns: minmax(20rem, 30rem) minmax(calc(var(--num-cols) * 15rem), calc(var(--num-cols) * 30rem)) 5rem;
        }

        &.mod-grades {
            grid-template-columns: minmax(20rem, 30rem) minmax(calc(var(--num-cols) * 15rem), calc(var(--num-cols) * 30rem));
        }
    }
    .app-header-tools.mod-entry-view {
        grid-column-start: 1;
    }
    .treenode-header.mod-responsive {
        grid-column-start: 1;
    }
    .subtotal.mod-entry-view {
        grid-column-start: 2;
    }

    @media only screen and (min-width: 900px) {
        .levels-table-header.mod-entry-view {
            grid-column-start: 2;
        }
    }

    @media only screen and (max-width: 899px) {
        .rubric.mod-scores {
            grid-template-columns: minmax(calc(var(--num-cols) * 5rem), calc(var(--num-cols) * 30rem)) 5rem;
        }

        .rubric.mod-grades {
            grid-template-columns: minmax(calc(var(--num-cols) * 5rem), calc(var(--num-cols) * 30rem));
        }

        .app-header-tools.mod-entry-view {
            grid-column: 1 / -1;
        }

        .levels-table-header.mod-entry-view {
            grid-column: 1 / -1;
        }
        .criterium-levels-wrapper {
            grid-column: 1 / -1;
        }
    }
    @media only screen and (max-width: 679px) {
        .levels-table-header.mod-entry-view {
            display: none;
        }
    }
</style>
<style lang="scss" scoped>
    .cluster-row.mod-entry-view {
        display: flex;
        margin-left:.3em;
    }

    .cluster-title.mod-entry-view {
        margin:.1em 0;margin-right:2.4rem;
    }

    .category-row.mod-entry-view {
        display: flex;
        margin-left:.3em;
    }

    .category-title.mod-entry-view {
        margin: .1em 2.4rem .1em -.3em;
    }

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

    .cluster-header {
        margin: .25em .5em 0 0em;
        padding-left: 1.1em;
        position: relative;
    }

    .category-header {
        margin-right: .5em;
        margin-top: .25em;
        position: relative;
    }

    .btn-show-feedback.mod-cluster {
        top: 0;
    }

    .btn-show-feedback.mod-category {
        top: 0;
    }

    .criterium-levels-wrapper {
        display: flex;
        flex: 1;
        flex-direction: column;
    }

    .criterium-levels {
        display: flex;
        flex: 1;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .mod-entry-view {
        &.rubric-table-header {
            display: flex;
        }

        &.app-header-tools {
            /*background-color: hsla(190, 35%, 75%, 0.2);*/
            flex: 1;
            margin-right: 1rem;
            max-width: 30rem;
            min-width: 20rem;

            &.mod-demo {
                padding-left: 1.2em;
            }
        }

        &.levels-table-header {
            flex: 1;
            margin-left: 0;
            margin-right: 0;
        }

        &.criterium-header {
            /*border-top: 1px solid $score-light;*/
        }

        &.criterium-title {
            margin-right: 2.4rem;
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

            .level-icon-check {
                opacity: .5;
            }
        }

        &.is-selected {
            &:hover, &:focus {
                box-shadow: inset 0 0 0 1px white;

                .level-icon-check {
                    opacity: 1;
                }
            }
        }
    }

    .criterium-level-title, .score-number, .graded-level {
        &.is-selected {
            color: #fff;
        }
    }

    .fa.level-icon-check {
        opacity: 0.2;
        font-size: 1.3rem;
        transition: opacity 200ms, font-size 200ms;

        &.is-selected {
            opacity: 1;
            font-size: 1.6rem;
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
        white-space: pre-line;

        &.is-feedback-visible {
            display: block;
        }
    }

    .custom-feedback {
        border-radius: $border-radius;
        display: none;
        margin-bottom: 1em;
        margin-top: .5em;

        &.mod-default-feedback {
            margin-top: 1em;
        }

        &.is-feedback-visible {
            display: block;
        }

    }

    .ta-custom-feedback {
        border: 1px solid #d0d0d0;
        border-radius: $border-radius;
        display: block;
        height: 2.2em;
        max-width: 70ch;
        padding: .2em .4em 0;
        resize: none;
        width: 70ch;
        width: 100%;

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
            resize: vertical;

            &::placeholder {
                color: #666;
            }
        }
    }

    .rubric-entry-error {
        align-self: flex-start;
        /*border-bottom: 2px solid red;*/
        color: red;
        padding: 0 .25em;
    }

    @media only screen and (max-width: 679px) {
        .rubric-entry-view {
            max-width: 75ch;
            /*width: 40em;*/
        }

        .mod-entry-view {
            &.levels-table-header {
                display: none;
            }

            &.criterium-level:not(:first-child) {
                margin-top: .3em;
            }

            &.criterium-level {
                /*margin-left: .8em;*/
                /*max-width: 75ch;*/
            }

            &.subtotal {
                margin-right: .5em;
                /*max-width: 75ch;*/
            }

            &.criterium-total {
                display: none;
            }
        }

        .criterium-levels {
            flex-direction: column;
        }
    }

    @media only screen and (min-width: 680px) {
        .level-table-header-title.mod-entry-view {
            max-width: 33rem;
        }

        .criterium-level.mod-entry-view {
            background: hsla(0, 0, 98%, 1);
            border-radius: 3px;
            max-width: 33rem;
            border-bottom: 1px solid #e0e0e0;
        }

        .criterium-level-title.mod-entry-view {
            height: 1px;
            left: -10000px;
            overflow: hidden;
            position: absolute;
            top: auto;
            width: 1px;
        }

        &.levels-table-header.mod-entry-view.is-using-scores {
            margin-right: 4em;
        }

        .u-resize {
            transform: translateX(var(--offset));
        }
    }

    @media only screen and (min-width: 680px) and (max-width: 899px) {
        .clusters.mod-entry-view {
            margin-top: 1em;
        }
    }

    @media only screen and (max-width: 899px) {
        .rubric-entry-error {
            margin-left: 1em;
        }

        .rubric-entry-view {
            max-width: 100%;
            /*width: 40em;*/
            &.mod-closed .rubric-entry-error {
                margin-left: 2.3em;
            }
        }

        .criterium-levels {
            margin-left: 1em;
        }

        .mod-entry-view {
         /*   &.levels-table-header {
                display: none;
            }*/

            &.criterium.mod-responsive {
                margin-bottom: 1em;
            }

            &.cluster-row {
                flex-direction: column;
            }

            &.category-row {
                flex-direction: column;
            }

            &.rubric-table-header {
                flex-direction: column;
            }

            &.levels-table-header {
                margin-left: 1.2em;
            }

            &.criterium-level-header {
                align-content: center;
                display: flex;
                justify-content: center;
                justify-items: center;
                /*margin-top: .5em;*/
                padding: 0 .25em;
                text-align: left;
            }

            &.criterium-total {
                margin-right: .5em;
                /*max-width: 41.25em;*/
            }

            &.score-number-calc.mod-criterium {
                /*margin-top: .335em;*/
                margin-bottom: 0;
            }

            &.criterium-header {
                max-width: 75ch;
                margin-bottom: .25em;
            }
        }

        .app-header-tools.mod-entry-view {
            padding-left: 1.55rem;
        }

        .rubric-entry-view.mod-closed {
            .criterium-levels, .custom-feedback, .levels-table-header.mod-entry-view, .cluster-title.mod-entry-view, .category-title.mod-entry-view, .criterium-title.mod-entry-view {
                padding-left: 1.55rem;
            }
        }

        .btn-show-feedback {
            left: -.5em;
        }

        &.cluster-header, &.category-header {
            max-width: 75ch;
        }
        .default-feedback-entry-view {
            max-width: 40em;
        }

        .custom-feedback {
            margin: .5em .5em 0 1em;

            &.mod-cluster {
                margin: 0em .5em .5em 1em;
            }

            &.mod-category {
                margin: 0em .5em .5em 1em;
            }

            &.mod-cluster, &.mod-category {
                padding-top: .5em;
                padding-bottom: .5em;
                /*padding: .5em 0;*/
            }
        }
    }

    @media only screen and (min-width: 900px) {
        .rubric-entry-view {
            /*max-width: max-content;*/
        }

        .mod-entry-view {
            &.criterium {
                align-items: baseline;
            }

            &.criterium-level:nth-last-child(2) {
                margin-right: 1em;
            }

            &.criterium-header {
                flex: 1;
                min-width: 20rem;
                max-width: 30rem;
            }
        }

        .custom-feedback {
            margin-right: .5em;

            &.mod-scores {
                margin-right: 4.5em;
            }

            &.mod-cluster {
                flex: 1;
                align-self: center;
                margin: .25em 0 .5em 0;
            }

            &.mod-category {
                flex: 1;
                align-self: center;
            }

            &.mod-criterium {
                /*background: hsla(226, 19%, 72%, .3);*/
                /*padding: .5em .2em;*/
            }
        }


        .cluster-header {
            align-self: center;
            flex: 1;
            max-width: 30rem;
            min-width: 20rem;
        }

        .category-header {
            align-self: center;
            flex: 1;
            max-width: 30rem;
            min-width: 20rem;
        }


        .treenode-header {
            h2, h3, h4 {
                cursor: default;
            }
        }
    }
    .btn-icon-show-feedback {
        opacity: 0;

        &.is-feedback-visible {
            opacity: 1;
        }
    }

    @media (pointer: coarse) {
        .btn-icon-show-feedback {
            opacity: 1;
        }
    }

    .treenode-header:hover .btn-icon-show-feedback {
        opacity: 1;
    }

    .criterium:hover .btn-icon-show-feedback {
        opacity: 1;
    }

    /*    .treenode-hover::before {
        content: '';
        display: inline-block;
        width: 4px;
        background: transparent;
        justify-self: stretch;
        margin-left: -10px;
        margin-right: 6px;
    }

    .treenode-hover {
    }

    .treenode-hover:hover::before {
        background: #66aacc8f;
    }
    .treenode-hover:hover {
    }*/
</style>
<style lang="scss">
.default-feedback-entry-view {
    white-space: initial!important;
}
.default-feedback-entry-view {
    ul {
        list-style: disc;
    }

    ul, ol {
        margin: 0 0 0 2rem;
        padding: 0;
    }
}
</style>