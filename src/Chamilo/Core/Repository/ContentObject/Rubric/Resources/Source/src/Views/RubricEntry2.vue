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
            <ul class="rubric-header mod-responsive">
                <li class="rubric-header-title" v-for="level in rubric.levels">{{ level.title }}</li>
            </ul>
            <div class="rubric-header-fill"></div>
            <template v-for="{cluster, ext, evaluation, score} in getClusterRowsData(rubric)">
                <div class="treenode-title-header mod-responsive mod-entry">
                    <div class="treenode-title-header-pre"></div>
                    <h2 class="treenode-title cluster-title">{{ cluster.title }}</h2>
                    <button v-if="!preview && !showDefaultFeedbackFields" class="btn-show" :aria-label="$t('show-default-description')" :title="$t('show-default-description')" @click.prevent="ext.showDefaultFeedback = !ext.showDefaultFeedback">
                        <i tabindex="-1" class="btn-icon-show-feedback fa" :class="{'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback}" aria-hidden="true" />
                    </button>
                </div>
                <div v-if="!preview && evaluation && (showDefaultFeedbackFields || ext.showDefaultFeedback)" class="treenode-custom-feedback">
                    <textarea class="ta-custom-feedback" :placeholder="$t('extra-feedback')" v-model="evaluation.feedback" @input="onTreeNodeFeedbackChanged(evaluation)"></textarea>
                </div>
                <template v-for="{category, ext, evaluation} in getCategoryRowsData(cluster)">
                    <div v-if="category.title" class="treenode-title-header mod-responsive mod-entry" :style="`--category-color: ${ category.title && category.color ? category.color : 'transparent' }`">
                        <div class="treenode-title-header-pre mod-category"></div>
                        <h3 class="treenode-title category-title">{{ category.title }}</h3>
                        <button v-if="!preview && !showDefaultFeedbackFields" class="btn-show" :aria-label="$t('show-default-description')" :title="$t('show-default-description')" @click.prevent="ext.showDefaultFeedback = !ext.showDefaultFeedback">
                            <i tabindex="-1" class="btn-icon-show-feedback fa" :class="{'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback}" aria-hidden="true" />
                        </button>
                    </div>
                    <div v-if="!preview && evaluation && category.title && (showDefaultFeedbackFields || ext.showDefaultFeedback)" class="treenode-custom-feedback">
                        <textarea class="ta-custom-feedback" :placeholder="$t('extra-feedback')" v-model="evaluation.feedback" @input="onTreeNodeFeedbackChanged(evaluation)"></textarea>
                    </div>
                    <template v-for="{criterium, ext, evaluation, score} in getCriteriumRowsData(category)">
                        <div class="treenode-title-header mod-responsive mod-entry" :style="`--category-color: ${ !(category.title && category.color) ? '#999' : category.color }`">
                            <div class="treenode-title-header-pre mod-criterium"></div>
                            <h4 :id="`criterium-${criterium.id}-title`" class="treenode-title criterium-title">{{ criterium.title }}</h4>
                            <button v-if="!showDefaultFeedbackFields" class="btn-show" :aria-label="$t('show-default-description')" :title="$t('show-default-description')" @click.prevent="ext.showDefaultFeedback = !ext.showDefaultFeedback">
                                <i tabindex="-1" class="btn-icon-show-feedback fa" :class="{'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback}" aria-hidden="true" />
                            </button>
                        </div>
                        <div class="treenode-rubric-input">
                            <!--<div v-if="showErrors && !preview && !hasSelection()" class="rubric-entry-error">{{ $t('select-level') }}</div>-->
                            <div class="treenode-choices">
                                <div class="treenode-choice" :class="{'mod-has-feedback': (showDefaultFeedbackFields || ext.showDefaultFeedback ) && choice.feedback }" v-for="{choice, isSelected} in getChoicesColumnData(ext, evaluation)">
                                    <component :is="preview ? 'div' : 'button'" class="treenode-level" :class="{ 'is-selected': isSelected, 'mod-btn': !preview }" @click="preview ? null : selectLevel(evaluation, choice.level)">
                                        <span class="treenode-level-title">{{ choice.level.title }}</span>
                                        <span v-if="rubric.useScores" :aria-label="`${ choice.score } ${ $t('points') }`">{{ choice.score }}</span>
                                        <span v-else><i class="treenode-level-icon-check fa fa-check" :class="{ 'is-selected': isSelected }" /></span>
                                    </component>
                                    <div v-if="choice.feedback && (showDefaultFeedbackFields || ext.showDefaultFeedback)" class="treenode-level-description" :class="{'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback }">{{ choice.feedback }}</div>
                                </div>
                            </div>
                            <div v-if="evaluation && (showDefaultFeedbackFields || ext.showDefaultFeedback)" class="treenode-custom-feedback">
                                <textarea class="ta-custom-feedback" :placeholder="$t('extra-feedback')" v-model="evaluation.feedback" @input="onTreeNodeFeedbackChanged(evaluation)"></textarea>
                            </div>
                        </div>
                        <div v-if="rubric.useScores" class="treenode-score">
                            <div class="treenode-score-calc mod-criterium"><span class="sr-only">{{ $t('total') }}:</span> {{ preview ? 0 : score }} <span class="sr-only">{{ $t('points') }}</span></div>
                        </div>
                    </template>
                </template>
                <template v-if="rubric.useScores">
                    <div class="total-title">{{ $t('total') }} {{ $t('subsection') }}:</div>
                    <div class="treenode-score-calc mod-cluster">{{ score }}</div>
                </template>
                <div class="cluster-sep" :class="{ 'mod-grades': !rubric.useScores }"></div>
            </template>
            <template v-if="rubric.useScores">
                <!--<slot name="slot-inner"></slot>-->
                <div class="total-title">{{ $t('total') }} {{ $t('rubric') }}:</div>
                <div class="treenode-score-calc mod-rubric">{{ getRubricScore() }}</div>
                <div class="total-title">Maximum:</div>
                <div class="treenode-score-calc mod-rubric-max">{{ rubric.getMaximumScore() }}</div>
            </template>
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

        @Prop({type: Rubric}) readonly rubric!: Rubric;
        @Prop({type: Array, default: () => []}) readonly treeNodeEvaluations!: TreeNodeEvaluation[];
        @Prop({type: Object}) readonly uiState!: any;
        @Prop({type: Object, default: () => ({})}) readonly options!: any;
        @Prop({type: Boolean, default: false}) readonly preview!: boolean;
        @Prop({type: Boolean, default: false}) readonly showErrors!: boolean;

        marked(rawString: string) {
            return DOMPurify.sanitize(marked(rawString));
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

    .btn-show {
        background: none;
        border: 1px solid transparent;
        color: #b3b3b3;
        cursor: pointer;
        display: flex;
        height: 1.8rem;
        justify-content: center;
        margin-left: .5rem;
        width: 1.8rem;

        &:hover {
            color: #999;
        }

        &:focus {
            outline: none;

            .btn-icon-show-feedback {
                border: 1px solid $input-color-focus;
            }
        }
    }

    .btn-icon-show-feedback {
        opacity: 0;
        transform: rotate(90deg);
        transition: transform 300ms;

        &.is-feedback-visible {
            opacity: 1;
            transform: rotate(0deg);
        }

        &::before {
            content: '\f077';
        }

        &:focus {
            outline: none;
        }
    }

    .treenode-title-header:hover .btn-icon-show-feedback {
        opacity: 1;
    }

    .treenode-choice.mod-has-feedback {
        background: #fafafa;
        border-bottom: 1px solid #e0e0e0;
        border-radius: $border-radius;
        margin-bottom: .7rem;
    }

    .treenode-level {
        &.is-selected {
            background: $level-selected-color;
            color: #fff;
        }

        &.mod-btn {
            cursor: pointer;
            outline: none;

            &:hover, &:focus {
                border: 1px solid $level-selected-color;

                .treenode-level-icon-check {
                    opacity: .5;
                }
            }

            &.is-selected {
                &:hover, &:focus {
                    box-shadow: inset 0 0 0 1px white;

                    .treenode-level-icon-check {
                        opacity: 1;
                    }
                }
            }
        }
    }

    .treenode-level-icon-check.fa {
        font-size: 1.3rem;
        opacity: 0.2;
        transition: opacity 200ms, font-size 200ms;

        &.is-selected {
            font-size: 1.6rem;
            opacity: 1;
        }
    }

    .treenode-level-description {
        display: none;
        font-size: 1.3rem;
        line-height: 1.8rem;
        padding: .35rem .65rem;
        white-space: pre-line;

        &.is-feedback-visible {
            display: block;
        }
    }

    .treenode-custom-feedback {
        grid-column-start: 2;
        padding: .2rem;
        z-index: 10;
    }

    .ta-custom-feedback {
        border: 1px solid #d0d0d0;
        border-radius: $border-radius;
        display: block;
        height: 2.2em;
        max-width: 70ch;
        padding: .2em .4em 0;
        resize: none;
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

    .treenode-score-calc {
        border-radius: $border-radius;
        font-size: 1.8rem;
        line-height: 2.88rem;
        padding-right: .5rem;
        text-align: right;

        &.mod-criterium {
            background: $score-lighter;
            color: #666;
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
    }
</style>
<style lang="scss" scoped>
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

    .btn-show-feedback.mod-cluster {
        top: 0;
    }

    .btn-show-feedback.mod-category {
        top: 0;
    }

    .mod-entry-view {
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

        &.score-number-calc.mod-criterium {
            background: $score-lighter;
            line-height: 1.6em;
            margin-bottom: -1px;
            padding-top: 1px;
        }
    }

    .app-tool-item {
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


    .rubric-entry-error {
        align-self: flex-start;
        /*border-bottom: 2px solid red;*/
        color: red;
        padding: 0 .25em;
    }

    @media only screen and (max-width: 679px) {
        .treenode-custom-feedback {
            grid-column: 1 / -1;
        }

        .treenode-score {
            display: none;
        }

        .rubric-entry-view {
            max-width: 75ch;
            /*width: 40em;*/
        }

        .mod-entry-view {
            &.subtotal {
                margin-right: .5em;
                /*max-width: 75ch;*/
            }

            &.criterium-total {
                display: none;
            }
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

        .treenode-custom-feedback {
            grid-column-start: 1;
            margin-left: 1.8rem;
        }

        .mod-entry-view {
            &.score-number-calc.mod-criterium {
                /*margin-top: .335em;*/
                margin-bottom: 0;
            }
        }

        .app-header-tools.mod-entry-view {
            padding-left: 1.55rem;
        }

        .btn-show-feedback {
            left: -.5em;
        }

        .default-feedback-entry-view {
            max-width: 40em;
        }
    }

    @media only screen and (min-width: 900px) {
        .rubric-entry-view {
            /*max-width: max-content;*/
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

    /*.treenode-header:hover .btn-icon-show-feedback {
        opacity: 1;
    }

    .criterium:hover .btn-icon-show-feedback {
        opacity: 1;
    }

        .treenode-hover::before {
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