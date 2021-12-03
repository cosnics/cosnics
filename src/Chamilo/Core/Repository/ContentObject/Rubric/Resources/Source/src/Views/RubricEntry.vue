<i18n>
{
    "en": {
        "expand-all": "Expand all",
        "extra-feedback": "Enter extra feedback",
        "feedback": "Feedback",
        "points": "points",
        "rubric": "Rubric",
        "select-level": "Select a level",
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
        "select-level": "Selectionnez un niveau",
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
        "select-level": "Selecteer een niveau",
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
        <div class="rubric mod-entry" :class="[{ 'is-demo-inactive': this.options.isDemo && !this.options.evaluator }, useScores ? 'mod-scores' : 'mod-grades', { 'mod-weights': useScores && rubric.useRelativeWeights }]" :style="{'--num-cols': rubric.levels.length}">
            <ul class="rubric-tools">
                <slot name="demoEvaluator"></slot>
                <li class="app-tool-item" :class="{ 'is-demo-inactive': this.options.isDemo && !this.options.evaluator }"><button class="btn-check" :aria-label="$t('show-default-descriptions')" :aria-expanded="showDefaultFeedbackFields ? 'true' : 'false'" :class="{ checked: showDefaultFeedbackFields }" @click.prevent="toggleDefaultFeedbackFields"><span class="lbl-check" tabindex="-1"><i class="btn-icon-check fa" aria-hidden="true" />{{ options.isDemo ? $t('feedback') : $t('expand-all') }}</span></button></li>
            </ul>
            <ul class="rubric-header mod-responsive">
                <li class="rubric-header-title" v-for="level in rubric.levels"><!--<span v-if="useScores && rubric.useRelativeWeights" style="background-color: rgba(0, 0, 0, .1); border-radius: 3px; float: right; font-weight: 600; padding: 0 5px">{{level.score}}</span>-->{{ level.title }}</li>
            </ul>
            <div v-if="useScores && rubric.useRelativeWeights" class="rubric-relative-weights-header mod-responsive">
                <div class="rubric-header-title" style="background: white;color: #5f929d;box-shadow: inset 0 0 1px 1px #ecf1f2;display: flex;padding: 0;">
                    <div style="flex: 1;text-align: center;border-right: 1px inset #ecf1f2;padding: .8rem .7rem;font-weight:600">Tot. <i class="fa fa-percent" style="font-size: 1.1rem;align-self: center;"></i></div>
                    <div style="flex: 1;text-align: center;padding: .8rem .7rem;font-weight:600">Max. <i class="fa fa-percent" style="font-size: 1.1rem;align-self: center;"></i></div>
                </div>
            </div>
            <div v-else class="rubric-header-fill"></div>
            <template v-for="{cluster, ext, evaluation, score} in getClusterRowsData(rubric)">
                <div class="treenode-title-header mod-responsive mod-entry" :class="{ 'is-highlighted': highlightedTreeNode === cluster }" @mouseover="highlightedTreeNode = cluster" @mouseout="highlightedTreeNode = null">
                    <div class="treenode-title-header-pre"></div>
                    <h1 class="treenode-title cluster-title">{{ cluster.title }}</h1>
                    <button v-if="!preview && !showDefaultFeedbackFields" class="btn-show" :aria-label="$t('show-default-description')" :title="$t('show-default-description')" @click.prevent="ext.showDefaultFeedback = !ext.showDefaultFeedback">
                        <i tabindex="-1" class="btn-icon-show-feedback fa" :class="{'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback}" aria-hidden="true" />
                    </button>
                </div>
                <div v-if="!preview && evaluation && (showDefaultFeedbackFields || ext.showDefaultFeedback)" class="treenode-custom-feedback" @mouseover="highlightedTreeNode = cluster" @mouseout="highlightedTreeNode = null">
                    <textarea class="ta-custom-feedback" :placeholder="$t('extra-feedback')" v-model="evaluation.feedback" @input="onTreeNodeFeedbackChanged(evaluation)"></textarea>
                </div>
                <template v-for="{category, ext, evaluation} in getCategoryRowsData(cluster)">
                    <div v-if="category.title" class="treenode-title-header mod-responsive mod-entry" :class="{ 'is-highlighted': highlightedTreeNode === category }" :style="`--category-color: ${ category.title && category.color ? category.color : 'transparent' }`" @mouseover="highlightedTreeNode = category" @mouseout="highlightedTreeNode = null">
                        <div class="treenode-title-header-pre mod-category"></div>
                        <h2 class="treenode-title category-title">{{ category.title }}</h2>
                        <button v-if="!preview && !showDefaultFeedbackFields" class="btn-show" :aria-label="$t('show-default-description')" :title="$t('show-default-description')" @click.prevent="ext.showDefaultFeedback = !ext.showDefaultFeedback" @mouseover="highlightedTreeNode = category" @mouseout="highlightedTreeNode = null">
                            <i tabindex="-1" class="btn-icon-show-feedback fa" :class="{'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback}" aria-hidden="true" />
                        </button>
                    </div>
                    <div v-if="!preview && evaluation && category.title && (showDefaultFeedbackFields || ext.showDefaultFeedback)" class="treenode-custom-feedback">
                        <textarea class="ta-custom-feedback" :placeholder="$t('extra-feedback')" v-model="evaluation.feedback" @input="onTreeNodeFeedbackChanged(evaluation)"></textarea>
                    </div>
                    <template v-for="{criterium, ext, evaluation, score} in getCriteriumRowsData(category)">
                        <div class="treenode-title-header mod-responsive mod-entry" :class="{'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback, 'mod-no-default-feedback': !anyChoicesFeedback(ext), 'is-highlighted': highlightedTreeNode === criterium}" :style="`--category-color: ${ !(category.title && category.color) ? '#999' : category.color }`" @mouseover="highlightedTreeNode = criterium" @mouseout="highlightedTreeNode = null">
                            <div class="treenode-title-header-pre mod-criterium"></div>
                            <h3 :id="`criterium-${criterium.id}-title`" class="treenode-title criterium-title u-markdown-criterium" v-html="criterium.toMarkdown()"></h3>
                            <button v-if="!showDefaultFeedbackFields" class="btn-show" :aria-label="$t('show-default-description')" :title="$t('show-default-description')" @click.prevent="ext.showDefaultFeedback = !ext.showDefaultFeedback">
                                <i tabindex="-1" class="btn-icon-show-feedback fa" :class="{'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback}" aria-hidden="true" />
                            </button>
                        </div>
                        <div class="treenode-rubric-input" @mouseover="highlightedTreeNode = criterium" @mouseout="highlightedTreeNode = null">
                            <div v-if="showErrors && !preview && !(evaluation && evaluation.level)" class="rubric-entry-error">{{ $t('select-level') }}</div>
                            <div class="treenode-choices">
                                <div class="treenode-choice" :class="{'mod-has-feedback': (showDefaultFeedbackFields || ext.showDefaultFeedback ) && choice.feedback }" v-for="{choice, isSelected} in getChoicesColumnData(ext, evaluation)">
                                    <component :is="preview ? 'div' : 'button'" class="treenode-level" :class="{ 'is-selected': isSelected, 'mod-btn': !preview }" @click="preview ? null : selectLevel(evaluation, choice.level)">
                                        <span class="treenode-level-title">{{ choice.level.title }}</span>
                                        <span v-if="useScores && rubric.useRelativeWeights" :aria-label="`${ choice.level.score } ${ $t('points') }`">{{ choice.level.score }}</span>
                                        <span v-else-if="useScores" :aria-label="`${ choice.score.toLocaleString() } ${ $t('points') }`">{{ choice.score.toLocaleString() }}</span>
                                        <span v-else>
                                            <i class="treenode-level-icon-check fa fa-check" :class="{ 'is-selected': isSelected }" />
                                        </span>
                                    </component>
                                    <div v-if="choice.feedback && (showDefaultFeedbackFields || ext.showDefaultFeedback)" class="treenode-level-description" :class="{'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback }" v-html="choice.choice.toMarkdown()"></div>
                                </div>
                            </div>
                            <div v-if="evaluation && (showDefaultFeedbackFields || ext.showDefaultFeedback)" class="treenode-custom-feedback">
                                <textarea class="ta-custom-feedback" :placeholder="$t('extra-feedback')" v-model="evaluation.feedback" @input="onTreeNodeFeedbackChanged(evaluation)"></textarea>
                            </div>
                        </div>
                        <div v-if="useScores" class="treenode-score mod-rel-weight">
                            <div v-if="rubric.useRelativeWeights" class="treenode-score-calc mod-criterium mod-rel-weight">
                                <div class="treenode-score-rel-total mod-criterium"><span class="sr-only">{{ $t('total') }}:</span><score-display :score="preview ? 0 : score" show-percent /></div>
                                <div class="treenode-score-rel-max"><score-display :score="criterium.rel_weight !== null ? criterium.rel_weight : rubric.eqRestWeight" show-percent /></div>
                            </div>
                            <div v-else class="treenode-score-calc mod-criterium">
                                <span class="sr-only">{{ $t('total') }}:</span> {{ preview ? 0 : score.toLocaleString() }} <span class="sr-only">{{ $t('points') }}</span>
                            </div>
                        </div>
                    </template>
                </template>
                <template v-if="useScores">
                    <div class="total-title">{{ $t('total') }} {{ $t('subsection') }}:</div>
                    <div v-if="rubric.useRelativeWeights" class="treenode-score-calc mod-cluster mod-rel-weight">
                        <div class="treenode-score-rel-total mod-cluster"><score-display :score="score" show-percent :mute-fraction="false" /></div>
                        <div class="treenode-score-rel-max"><score-display :score="rubric.getClusterMaxScore(cluster)" show-percent :mute-fraction="false" /></div>
                    </div>
                    <div v-else class="treenode-score-calc mod-cluster">{{ score.toLocaleString() }}</div>
                </template>
                <div class="cluster-sep" :class="{ 'mod-grades': useGrades }"></div>
            </template>
            <slot name="slot-inner"></slot>
            <template v-if="useScores">
                <div class="total-title">{{ $t('total') }} {{ $t('rubric') }}:</div>
                <div v-if="rubric.useRelativeWeights" class="treenode-score-calc mod-rubric mod-rel-weight">
                    <div class="treenode-score-rel-total mod-rubric"><score-display :score="getRubricScore()" show-percent :mute-fraction="false" /></div>
                    <div class="treenode-score-rel-max"><score-display :score="rubric.getMaximumScore()" show-percent :mute-fraction="false" /></div>
                </div>
                <div v-else class="treenode-score-calc mod-rubric">{{ getRubricScoreRounded().toLocaleString() }}</div>
                <template v-if="!rubric.useRelativeWeights">
                    <div class="total-title">Maximum:</div>
                    <div class="treenode-score-calc mod-rubric-max">{{ rubric.getMaximumScore().toLocaleString() }}</div>
                </template>
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
    import ScoreDisplay from '../Components/ScoreDisplay.vue';
    import {TreeNodeEvaluation, TreeNodeExt} from '../Util/interfaces';

    function add(v1: number, v2: number) {
        return v1 + v2;
    }

    function rounded2dec(v: number) {
        return Math.round(v * 100) / 100;
    }

    @Component({
        components: { ScoreDisplay }
    })
    export default class RubricEntry extends Vue {
        private treeNodeData: TreeNodeExt[] = [];
        private highlightedTreeNode: TreeNode|null = null;

        @Prop({type: Rubric}) readonly rubric!: Rubric;
        @Prop({type: Array, default: () => []}) readonly treeNodeEvaluations!: TreeNodeEvaluation[];
        @Prop({type: Object}) readonly uiState!: any;
        @Prop({type: Object, default: () => ({})}) readonly options!: any;
        @Prop({type: Boolean, default: false}) readonly preview!: boolean;
        @Prop({type: Boolean, default: false}) readonly showErrors!: boolean;

        anyChoicesFeedback(ext: TreeNodeExt) {
            return ext.choices.filter(choice => !!choice.feedback).length > 0;
        }

        getChoicesColumnData(ext: TreeNodeExt, evaluation: TreeNodeEvaluation|null) {
            return ext.choices.map(choice => ({
                choice,
                isSelected: this.preview || !evaluation ? choice.level.isDefault : choice.level === evaluation.level
            }));
        }

        get useScores() {
            return this.rubric.useScores;
        }

        get useGrades() {
            return !this.rubric.useScores;
        }

        getClusterRowsData(rubric: Rubric) {
            return rubric.clusters
                .filter(cluster => cluster.hasChildren())
                .map(cluster => ({
                    cluster,
                    score: rounded2dec(this.getClusterScore(cluster)),
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
                score: rounded2dec(this.getCriteriumScore(criterium)),
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
            const criterium = evaluation.treeNode as Criterium;
            if (this.rubric.useScores) {
                if (this.rubric.useRelativeWeights) {
                    const ls = level.score / Math.max.apply(null, this.rubric.levels.map(l => l.score));
                    const relWeight = criterium.rel_weight === null ? this.rubric.eqRestWeightPrecise : criterium.rel_weight;
                    evaluation.score = relWeight * ls;
                } else {
                    evaluation.score = this.rubric.getChoiceScore(criterium, level);
                }
            }
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

        getRubricScoreRounded() {
            return rounded2dec(this.getRubricScore());
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
<style lang="scss">
    .rubric {
        &.mod-scores {
            grid-template-columns: minmax(max-content, 23rem) minmax(calc(var(--num-cols) * 15rem), calc(var(--num-cols) * 30rem)) 5.6rem;
        }

        &.mod-scores.mod-weights {
            grid-template-columns: minmax(max-content, 23rem) minmax(calc(var(--num-cols) * 15rem), calc(var(--num-cols) * 30rem)) 14rem;
        }

        &.mod-grades {
            grid-template-columns: minmax(max-content, 23rem) minmax(calc(var(--num-cols) * 15rem), calc(var(--num-cols) * 30rem));
        }

        &.mod-entry > :not(.rubric-tools) {
            transition: opacity 200ms;
        }

        &.mod-entry.is-demo-inactive > :not(.rubric-tools) {
            opacity: 0;
            pointer-events: none;
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

    .rubric-relative-weights-header {
        position: sticky;
        top: 0;
        z-index: 30;
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

    .rubric-entry-error {
        color: red;
    }

    .treenode-title-header.mod-entry {
        position: relative;

        &::before {
            border-left: .5rem solid transparent;
            bottom: -.5rem;
            content: '';
            left: -1rem;
            position: absolute;
            right: -.7rem;
            top: -.5rem;
            transition: 200ms border;
        }

        &.is-highlighted::before {
            border-color: hsla(204, 45%, 53%, 1);
        }
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

        &.is-feedback-visible {
            display: block;
        }

        ul {
            list-style: disc;
        }

        ul, ol {
            margin: 0 0 0 2rem;
            padding: 0;
        }
    }

    .treenode-custom-feedback {
        align-self: center;
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

    @media (pointer: coarse) {
        .btn-icon-show-feedback {
            opacity: 1;
        }
    }

    .treenode-score {
        z-index: 10;
    }

    .treenode-score-calc {
        border-radius: $border-radius;
        font-size: 1.8rem;
        line-height: 2.88rem;
        padding-right: .5rem;
        text-align: right;

        &.mod-criterium {
            align-items: baseline;
            background: $score-lighter;
            color: #666;
        }

        &.mod-cluster {
            background: $score-dark;
            color: #fff;
        }

        &.mod-criterium, &.mod-cluster, &.mod-rubric {
            &.mod-rel-weight {
                align-items: baseline;
                background: none;
                display: flex;
                padding-right: 0;
            }
        }

        &.mod-rubric {
            background: $score-darker;
            color: #fff;
        }

        &.mod-rubric-max {
            background: hsla(207, 40%, 35%, 1);
            color: #fff;
        }

        .fa-percent {
/*            color: #7999af;*/
            /*font-size: 1.1rem;*/
        }
    }

    .treenode-score-rel-total {
        border-bottom-left-radius: 3px;
        border-top-left-radius: 3px;
        flex: 1;
        padding-right: 0.5rem;

        &.mod-criterium {
            background: #eaf0f1;
            color: #36717d;
        }

        &.mod-cluster {
            background: hsla(190, 40%, 45%, .75);
            color: white;
        }

        &.mod-rubric {
            background: #36717d;
            color: white;
        }
        /*position: relative;

        &::after {
            position: absolute;
            content: '';
            top: -0.1rem;
            bottom: -0.1rem;
            width: 0.4rem;
            background: white;
            right: -0.3rem;
        }*/
    }

    .treenode-score-rel-max {
        /*background: #e8edf3;*/
        border-bottom-right-radius: 3px;
        border-top-right-radius: 3px;
        box-shadow: inset 0 0 1px 1px #eaf0f1;
        color: #6388a1;
        flex: 1;
        padding-right: 0.5rem;
    }

    @media only screen and (min-width: 900px) {
        .treenode-title-header.mod-entry {
            align-self: center;

            &.is-feedback-visible {
                align-self: initial;
                padding-top: 3rem;

                &.mod-no-default-feedback {
                    padding-top: 0;
                }
            }
        }
    }

    @media only screen and (max-width: 899px) {
        .rubric.mod-scores {
            grid-template-columns: minmax(calc(var(--num-cols) * 5rem), calc(var(--num-cols) * 30rem)) 5.6rem;
        }

        .rubric.mod-scores.mod-weights {
            grid-template-columns: minmax(calc(var(--num-cols) * 5rem), calc(var(--num-cols) * 30rem)) 14rem;
        }

        .rubric.mod-grades {
            grid-template-columns: minmax(calc(var(--num-cols) * 5rem), calc(var(--num-cols) * 30rem));
        }

        .rubric-tools {
            grid-column: 1 / -1;
            max-width: 75ch;
            position: initial;
        }

        .rubric-entry-error {
            padding-left: 1.8rem;
        }

        .treenode-custom-feedback {
            grid-column-start: 1;
            margin-left: 1.8rem;
        }
    }

    @media only screen and (min-width: 680px) and (max-width: 899px) {
        .btn-show {
            margin-left: -.5rem;
            order: -1;
        }
    }

    /*@media only screen and (min-width: 680px) {
        .score-mod-relative {
            display: none;
        }
    }*/

    @media only screen and (max-width: 679px) {
        .rubric-relative-weights-header.mod-responsive {
            display: none;
        }

        .treenode-custom-feedback {
            grid-column: 1 / -1;
        }

        .treenode-score {
            display: none;
        }

        .treenode-score.mod-rel-weight {
            display: block;
            grid-column-start: 2;
        }

        /*.treenode-level-icon-check.mod-relative {
            display: none;
        }*/
    }
</style>