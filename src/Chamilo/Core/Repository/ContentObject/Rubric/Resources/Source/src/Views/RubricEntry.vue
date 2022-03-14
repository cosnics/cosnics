<i18n>
{
    "en": {
        "expand-all": "Expand all",
        "extra-feedback": "Enter extra feedback",
        "feedback": "Feedback",
        "no-description": "No description",
        "points": "points",
        "rubric": "Rubric",
        "select-level": "Select a level",
        "show-default-descriptions": "Show all level descriptions and feedback",
        "show-default-description": "Show level descriptions and feedback",
        "subsection": "Subsection",
        "total": "Total",
        "weight": "Weight"
    },
    "fr": {
        "expand-all": "Agrandir tout",
        "extra-feedback": "Feed-back supplémentaire",
        "feedback": "Feed-back",
        "no-description": "Pas de description",
        "points": "points",
        "rubric": "Rubrique",
        "select-level": "Selectionnez un niveau",
        "show-default-descriptions": "Afficher toutes descriptions de niveau et feed-back",
        "show-default-description": "Afficher descriptions de niveau et feed-back",
        "subsection": "Sous-section",
        "total": "Total",
        "weight": "Poids"
    },
    "nl": {
        "expand-all": "Alles uitklappen",
        "extra-feedback": "Geef bijkomende feedback",
        "feedback": "Feedback",
        "no-description": "Geen omschrijving",
        "points": "punten",
        "rubric": "Rubric",
        "select-level": "Selecteer een niveau",
        "show-default-descriptions": "Toon alle niveauomschrijvingen en feedback",
        "show-default-description": "Toon niveauomschrijvingen en feedback",
        "subsection": "Onderdeel",
        "total": "Totaal",
        "weight": "Gewicht"
    }
}
</i18n>

<template>
    <div class="rubric mod-entry" :class="[{ 'is-demo-inactive': this.options.isDemo && !this.options.evaluator }, useScores ? 'mod-scores' : 'mod-grades', { 'mod-rel-weights': useScores && rubric.useRelativeWeights, 'mod-abs-weights': useScores && rubric.hasAbsoluteWeights }]" :style="{'--num-cols': rubric.rubricLevels.length}">
        <ul class="rubric-tools">
            <slot name="demoEvaluator"></slot>
            <li class="app-tool-item" :class="{ 'is-demo-inactive': this.options.isDemo && !this.options.evaluator }"><button class="btn-check" :aria-label="$t('show-default-descriptions')" :aria-expanded="showDefaultFeedbackFields ? 'true' : 'false'" :class="{ checked: showDefaultFeedbackFields }" @click.prevent="toggleDefaultFeedbackFields"><span class="lbl-check" tabindex="-1"><i class="btn-icon-check fa" aria-hidden="true" />{{ options.isDemo ? $t('feedback') : $t('expand-all') }}</span></button></li>
        </ul>
        <div v-if="rubric.useScores && (rubric.useRelativeWeights || rubric.hasAbsoluteWeights)" class="treenode-weight-header">
            <span>{{ $t('weight') }}</span>
        </div>
        <template v-if="!rubric.hasCustomLevels">
            <ul class="rubric-header mod-responsive rb-md-max:col-start-1">
                <li class="rubric-header-title" v-for="level in rubric.rubricLevels"><!--<span v-if="useScores && rubric.useRelativeWeights" style="background-color: rgba(0, 0, 0, .1); border-radius: 3px; float: right; font-weight: 600; padding: 0 5px">{{level.score}}</span>-->{{ level.title }}</li>
            </ul>
            <div class="rubric-header-fill"></div>
        </template>
        <template v-for="{cluster, ext, evaluation, score} in getClusterRowsData(rubric)">
            <div class="treenode-title-header mod-responsive mod-entry rb-lg:col-start-1 rb-md-max:col-span-full" :class="{ 'is-highlighted': highlightedTreeNode === cluster }" @mouseover="highlightedTreeNode = cluster" @mouseout="highlightedTreeNode = null">
                <h1 class="treenode-title cluster-title">{{ cluster.title }}</h1>
                <button v-if="!preview && !showDefaultFeedbackFields" class="btn-show" :aria-label="$t('show-default-description')" :title="$t('show-default-description')" @click.prevent="ext.showDefaultFeedback = !ext.showDefaultFeedback">
                    <i tabindex="-1" class="btn-icon-show-feedback fa" :class="{'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback}" aria-hidden="true" />
                </button>
            </div>
            <div v-if="!preview && evaluation && (showDefaultFeedbackFields || ext.showDefaultFeedback)" class="treenode-custom-feedback rb-md:col-start-1 rb-sm:col-span-full" :class="[rubric.useScores && (rubric.useRelativeWeights || rubric.hasAbsoluteWeights) ? 'rb-lg:col-start-3' : 'rb-lg:col-start-2']" @mouseover="highlightedTreeNode = cluster" @mouseout="highlightedTreeNode = null">
                <textarea class="ta-custom-feedback" :placeholder="$t('extra-feedback')" v-model="evaluation.feedback" @input="onTreeNodeFeedbackChanged(evaluation)"></textarea>
            </div>
            <template v-for="{category, ext, evaluation} in getCategoryRowsData(cluster)">
                <div v-if="category.title" class="treenode-title-header mod-category has-category mod-responsive mod-entry rb-lg:col-start-1 rb-md-max:col-span-full" :class="{ 'is-highlighted': highlightedTreeNode === category }" :style="`--category-color: ${ category.title && category.color ? category.color : '#999' }`" @mouseover="highlightedTreeNode = category" @mouseout="highlightedTreeNode = null">
                    <div class="treenode-title-header-pre mod-category" :class="{'mod-no-color': !category.color}"></div>
                    <h2 class="treenode-title category-title">{{ category.title }}</h2>
                    <button v-if="!preview && !showDefaultFeedbackFields" class="btn-show" :aria-label="$t('show-default-description')" :title="$t('show-default-description')" @click.prevent="ext.showDefaultFeedback = !ext.showDefaultFeedback" @mouseover="highlightedTreeNode = category" @mouseout="highlightedTreeNode = null">
                        <i tabindex="-1" class="btn-icon-show-feedback fa" :class="{'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback}" aria-hidden="true" />
                    </button>
                </div>
                <div v-if="!preview && evaluation && category.title && (showDefaultFeedbackFields || ext.showDefaultFeedback)" class="treenode-custom-feedback rb-md:col-start-1 rb-sm:col-span-full" :class="[rubric.useScores && (rubric.useRelativeWeights || rubric.hasAbsoluteWeights) ? 'rb-lg:col-start-3' : 'rb-lg:col-start-2']">
                    <textarea class="ta-custom-feedback" :placeholder="$t('extra-feedback')" v-model="evaluation.feedback" @input="onTreeNodeFeedbackChanged(evaluation)"></textarea>
                </div>
                <template v-for="{criterium, ext, evaluation, score} in getCriteriumRowsData(category)">
                    <div class="treenode-title-header mod-responsive mod-entry rb-lg:col-start-1 rb-md-max:col-span-full" :class="{'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback, 'is-highlighted': highlightedTreeNode === criterium, 'has-category': !!category.title}" :style="`--category-color: ${ !(category.title && category.color) ? '#999' : category.color }`" @mouseover="highlightedTreeNode = criterium" @mouseout="highlightedTreeNode = null">
                        <div class="treenode-title-header-pre mod-criterium"></div>
                        <h3 :id="`criterium-${criterium.id}-title`" class="treenode-title criterium-title u-markdown-criterium" :class="{'mod-no-category': !category.title}" v-html="criterium.toMarkdown()"></h3>
                        <button v-if="!showDefaultFeedbackFields" class="btn-show" :aria-label="$t('show-default-description')" :title="$t('show-default-description')" @click.prevent="ext.showDefaultFeedback = !ext.showDefaultFeedback">
                            <i tabindex="-1" class="btn-icon-show-feedback fa" :class="{'is-feedback-visible': showDefaultFeedbackFields || ext.showDefaultFeedback}" aria-hidden="true" />
                        </button>
                    </div>
                    <div class="treenode-weight mod-responsive mod-entry mod-pad" v-if="rubric.useScores && (rubric.useRelativeWeights || rubric.hasAbsoluteWeights)"><span class="treenode-weight-title">{{ $t('weight') }}: </span><span>{{ rubric.hasAbsoluteWeights && rubric.filterLevelsByCriterium(criterium).length ? 100 : rubric.getCriteriumWeight(criterium)|formatNum }}</span><span class="sr-only">%</span><i class="fa fa-percent" aria-hidden="true"></i></div>
                    <div class="treenode-rubric-input rb-md:col-start-1 rb-sm:col-span-full" @mouseover="highlightedTreeNode = criterium" @mouseout="highlightedTreeNode = null">
                        <div v-if="showErrors && !preview && !(evaluation && evaluation.level)" class="rubric-entry-error">{{ $t('select-level') }}</div>
                        <tree-node-entry :rubric="rubric" :ext="ext" :evaluation="evaluation" :preview="preview" :show-default-feedback-fields="showDefaultFeedbackFields" @select="selectLevel"></tree-node-entry>
                        <div v-if="evaluation && (showDefaultFeedbackFields || ext.showDefaultFeedback)" class="treenode-custom-feedback rb-md:col-start-1 rb-sm:col-span-full">
                            <textarea class="ta-custom-feedback" :placeholder="$t('extra-feedback')" v-model="evaluation.feedback" @input="onTreeNodeFeedbackChanged(evaluation)"></textarea>
                        </div>
                    </div>
                    <div v-if="useScores" class="treenode-score mod-rel-weight rb-sm:col-start-2">
                        <div v-if="rubric.useRelativeWeights" class="treenode-score-calc mod-criterium mod-rel-weight">
                            <div class="treenode-score-rel-total mod-criterium"><span class="sr-only">{{ $t('total') }}:</span><score-display :score="preview ? 0 : score" percent /></div>
                        </div>
                        <div v-else class="treenode-score-calc mod-criterium">
                            <span class="sr-only">{{ $t('total') }}:</span> <score-display :score="preview ? 0 : score" /> <span class="sr-only">{{ $t('points') }}</span>
                        </div>
                    </div>
                </template>
            </template>
            <template v-if="useScores">
                <div class="total-title rb-md-max:col-start-1" :class="rubric.useScores && (rubric.useRelativeWeights || rubric.hasAbsoluteWeights) ? 'rb-lg:col-start-3' : 'rb-lg:col-start-2'">{{ $t('total') }} {{ $t('subsection') }}:</div>
                <div v-if="rubric.useRelativeWeights" class="treenode-score-calc mod-cluster mod-rel-weight">
                    <div class="treenode-score-rel-total mod-cluster"><score-display :score="score" percent /></div>
                </div>
                <div v-else class="treenode-score-calc mod-cluster"><score-display :score="score" /></div>
            </template>
            <div class="cluster-sep" :class="{ 'mod-hide-last': useGrades }"></div>
        </template>
        <slot name="slot-inner"></slot>
        <template v-if="useScores">
            <div class="total-title rb-md-max:col-start-1" :class="rubric.useScores && (rubric.useRelativeWeights || rubric.hasAbsoluteWeights) ? 'rb-lg:col-start-3' : 'rb-lg:col-start-2'">{{ $t('total') }} {{ $t('rubric') }}:</div>
            <div v-if="rubric.useRelativeWeights" class="treenode-score-calc mod-rubric mod-rel-weight">
                <div class="treenode-score-rel-total mod-rubric"><score-display :score="preview ? 0 : rubricEvaluation.getRubricScore()" percent /></div>
            </div>
            <div v-else class="treenode-score-calc mod-rubric"><score-display :score="preview ? 0 : rubricEvaluation.getRubricScore()" /></div>
            <template v-if="!rubric.useRelativeWeights">
                <div class="total-title rb-md-max:col-start-1" :class="rubric.useScores && (rubric.useRelativeWeights || rubric.hasAbsoluteWeights) ? 'rb-lg:col-start-3' : 'rb-lg:col-start-2'">Maximum:</div>
                <div class="treenode-score-calc mod-rubric-max"><score-display :score="rubric.getMaximumScore()"  /></div>
            </template>
        </template>
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
    import TreeNodeEntry from '../Components/TreeNodeEntry.vue';
    import {TreeNodeEvaluation, TreeNodeExt} from '../Util/interfaces';
    import RubricEvaluation from '../Domain/RubricEvaluation';

    @Component({
        components: { ScoreDisplay, TreeNodeEntry },
        filters: {
            formatNum: function (v: number) {
                return v.toLocaleString(undefined, {maximumFractionDigits: 2});
            }
        }
    })
    export default class RubricEntry extends Vue {
        private treeNodeData: TreeNodeExt[] = [];
        private highlightedTreeNode: TreeNode|null = null;

        @Prop({type: Rubric}) readonly rubric!: Rubric;
        @Prop({type: RubricEvaluation}) readonly rubricEvaluation!: RubricEvaluation|undefined;
        @Prop({type: Object}) readonly uiState!: any;
        @Prop({type: Object, default: () => ({})}) readonly options!: any;
        @Prop({type: Boolean, default: false}) readonly preview!: boolean;
        @Prop({type: Boolean, default: false}) readonly showErrors!: boolean;
        @Prop({type: Object, default: null}) readonly existingResult!: any|null;

        getChoicesColumnData(ext: TreeNodeExt, evaluation: TreeNodeEvaluation|null) {
            if (ext.levels.length) {
                return ext.levels.map(level => ({
                    choice: null,
                    level,
                    isSelected: this.preview || !evaluation ? level.isDefault : level === evaluation.level
                }));
            }

            return ext.choices.map(choice => ({
                choice,
                level: null,
                isSelected: this.preview || !evaluation ? choice.level.isDefault : choice.level === evaluation.level
            }));
        }

        getItemsLength(ext: TreeNodeExt) {
            if (ext.levels.length) { return ext.levels.length; }
            return ext.choices.length;
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
                    score: this.preview ? 0 : this.rubricEvaluation?.getClusterScore(cluster),
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
                score: this.preview ? 0 : this.rubricEvaluation?.getCriteriumScore(criterium),
                ...this.getTreeNodeRowData(criterium)
            }));
        }

        getTreeNodeRowData(treeNode: TreeNode) {
            return {
                ext: this.getTreeNodeData(treeNode),
                evaluation: this.preview ? null : this.rubricEvaluation?.getTreeNodeEvaluation(treeNode)
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
            if (!this.rubricEvaluation) { return isDefaultLevel; }
            const evaluation = this.rubricEvaluation.getTreeNodeEvaluation(criterium);
            if (!evaluation || !evaluation.level) {
                return isDefaultLevel;
            }
            return evaluation.level === level;
        }

        selectLevel(evaluation: TreeNodeEvaluation, level: Level) : void {
            if (this.preview) { return; }
            evaluation.level = level;
            // careful: getChoiceScore will fail
            const criterium = evaluation.treeNode as Criterium;
            if (this.rubric.useScores) {
                if (this.rubric.useRelativeWeights) {
                    evaluation.score = level.score;
                } else {
                    if (level.criteriumId === criterium.id) {
                        evaluation.score = level.score;
                    } else {
                        evaluation.score = this.rubric.getChoiceScore(criterium, level);
                    }
                }
            }
            this.$emit('level-selected', evaluation.treeNode, level);
        }

        getTreeNodeData(treeNode: TreeNode) : TreeNodeExt|null {
            return this.treeNodeData.find((_ : TreeNodeExt) => _.treeNode === treeNode) || null;
        }

        private initData() {
            const rubric = this.rubric;
            this.treeNodeData = rubric.getAllTreeNodes().map(treeNode => {
                if (treeNode instanceof Criterium) {
                    const criteriumLevels = rubric.filterLevelsByCriterium(treeNode);
                    if (criteriumLevels.length) {
                        return { treeNode, levels: criteriumLevels, choices: [], showDefaultFeedback: false };
                    } else {
                        const choices = rubric.rubricLevels.map(level => {
                            const choice = rubric.getChoice(treeNode, level);
                            const score = rubric.getChoiceScore(treeNode, level);
                            return { title: level.title, feedback: choice?.feedback || '', score, choice, level};
                        });
                        return { treeNode, levels: [], choices, showDefaultFeedback: false };
                    }
                } else {
                    return { treeNode, levels: [], choices: [], showDefaultFeedback: false };
                }
            });
            if (rubric.useScores && !rubric.useRelativeWeights) {
                rubric.hasAbsoluteWeights = Rubric.usesAbsoluteWeights(rubric);
            }
            if (this.existingResult && this.rubricEvaluation) {
                const existingResults = this.existingResult.results;
                const rubricEvaluation = this.rubricEvaluation;

                this.rubric.getAllTreeNodes().forEach(treeNode => {
                    const existingResult = existingResults.find((r: any) => r.tree_node_id === parseInt(treeNode.id));
                    const evaluation = rubricEvaluation.getTreeNodeEvaluation(treeNode);
                    if (existingResult && evaluation) {
                        if (existingResult.comment) {
                            evaluation.feedback = existingResult.comment;
                        }
                        if (treeNode.getType() === 'criterium' && existingResult.level_id) {
                            const level = rubric.levels.find(l => existingResult.level_id === parseInt(l.id));
                            if (level) {
                                this.selectLevel(evaluation, level);
                            }
                        }
                    }
                });
                this.$emit('level-selected');
            }
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

        &.mod-scores.mod-abs-weights {
            grid-template-columns: minmax(max-content, 23rem) 7rem minmax(calc(var(--num-cols) * 15rem), calc(var(--num-cols) * 30rem)) 6.7rem;
        }

        &.mod-scores.mod-rel-weights {
            grid-template-columns: minmax(max-content, 23rem) 7rem minmax(calc(var(--num-cols) * 15rem), calc(var(--num-cols) * 30rem)) 8.3rem;
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

    .treenode-weight > .fa-percent {
        color: rgb(129, 169, 177);
        font-size: 1rem;
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

        > p, > em {
            margin: .2em 0;
        }

        &.is-feedback-visible {
            display: block;

            &.mod-no-default-feedback {
                opacity: .7;
                text-align: center;
            }
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
        line-height: 2.75rem;
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
            font-size: 1.1rem;
            opacity: .65;
        }
    }

    .treenode-score-rel-total {
        border-radius: 3px;
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
    }

    .treenode-weight {
        color: rgb(95, 146, 157);
    }

    @media only screen and (min-width: 900px) {
        .treenode-weight.mod-entry {
            padding-top: .25rem;
            text-align: center;
        }

        .treenode-title-header.mod-entry {
            padding-top: .6rem;
        }

        .treenode-weight-title {
            display: none;
        }
    }

    @media only screen and (max-width: 899px) {
        .rubric.mod-scores {
            grid-template-columns: minmax(calc(var(--num-cols) * 5rem), calc(var(--num-cols) * 30rem)) 5.6rem;
        }

        .rubric.mod-scores.mod-abs-weights {
            grid-template-columns: minmax(calc(var(--num-cols) * 5rem), calc(var(--num-cols) * 30rem)) 6.7rem;
        }

        .rubric.mod-scores.mod-rel-weights {
            grid-template-columns: minmax(calc(var(--num-cols) * 5rem), calc(var(--num-cols) * 30rem)) 8.3rem;
        }

        .rubric.mod-grades {
            grid-template-columns: minmax(calc(var(--num-cols) * 5rem), calc(var(--num-cols) * 30rem));
        }

        .rubric-tools {
            max-width: 75ch;
            position: initial;
        }

        .rubric-entry-error {
            padding-left: 1.8rem;
        }

        .treenode-custom-feedback {
            margin-left: 1.8rem;
        }

        .treenode-weight-title {
            color: hsl(180, 17%, 41%);
            font-weight: 700;
        }
    }

    @media only screen and (min-width: 680px) and (max-width: 899px) {
        .btn-show {
            margin-left: -.5rem;
            order: -1;
        }
    }

    @media only screen and (max-width: 679px) {
        .rubric-header-fill {
            display: none;
        }

        .rubric-relative-weights-header.mod-responsive {
            display: none;
        }

        .treenode-score {
            display: none;
        }

        .treenode-score.mod-rel-weight {
            display: block;
        }
    }
</style>

<style scoped>
    @media only screen and (max-width: 899px) {
        .treenode-weight-header {
            display: none;
        }
    }
</style>

<style lang="scss" scoped>


    @media only screen and (min-width: 900px) {
        .treenode-title-header.has-category::after {
            position: absolute;
            top: -0.5rem;
            width: 1px;
            bottom: -0.2rem;
            left: 0.6rem;
            background-color: var(--category-color);
            content: '';
            opacity: .5;
        }

        .treenode-title-header.mod-category::after {
            top: .8rem;
        }

        .criterium-title {
            margin-left: .75rem;
        }

        .criterium-title.mod-no-category {
            margin-left: .25rem;
        }
    }

    @media only screen and (max-width: 899px) {
        /*.treenode-title-header.has-category:not(.mod-category)::after {
            bottom: -4.1rem;
        }*/
    }

    @media only screen and (min-width: 680px) and (max-width: 899px) {
        .criterium-title {
            margin-left: -.75rem;
        }
        .btn-icon-show-feedback {
            background: white;
            margin-left: .75rem;
        }
        .btn-show {
            z-index: 20;
        }
    }

    @media only screen and (max-width: 679px) {
        .criterium-title {
            margin-left: .75rem;
        }
    }

    .treenode-title-header-pre.mod-category:after {
        border-radius: 50%;
    }

    .treenode-title-header-pre.mod-category.mod-no-color::after {
        border: 1px solid #bbb;
        background-color: #fff;
        width: 1.1rem;
        height: 1.1rem;
        position: absolute;
        left: .1rem;
    }

    .treenode-title-header-pre.mod-criterium::after {
        border: 1px solid var(--category-color);
        content: '';
        border-radius: 50%;
        height: 7px;
        margin-top: 0.3rem;
        width: 7px;
        position: absolute;
        left: 0.3rem;
        background-color: white;
    }

    .treenode-title.cluster-title {
        margin-left: .25rem;
    }

    .cluster-sep {
        border-color: #deebee;
        margin: 1rem 0 1.5rem;
    }
</style>