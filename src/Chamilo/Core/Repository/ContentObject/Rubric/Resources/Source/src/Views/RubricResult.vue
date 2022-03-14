<i18n>
{
    "en": {
        "extra-feedback": "Extra feedback",
        "no-results-available": "There are no results yet for this rubric.",
        "rubric": "Rubric",
        "total": "Total",
        "weight": "Weight"
    },
    "fr": {
        "extra-feedback": "Feed-back supplémentaire",
        "no-results-available": "Aucun résultat trouvé pour ce rubric.",
        "rubric": "Rubrique",
        "total": "Total",
        "weight": "Poids"
    },
    "nl": {
        "extra-feedback": "Extra feedback",
        "no-results-available": "Er zijn nog geen resultaten voor deze rubric beschikbaar.",
        "rubric": "Rubric",
        "total": "Totaal",
        "weight": "Gewicht"
    }
}
</i18n>

<template>
    <div v-if="rubric && evaluators.length" class="rubric-results-view">
        <div class="rubric" :class="useScores && rubric.useRelativeWeights && showScores ? 'mod-res-w' : 'mod-res'" :style="{'--num-cols': evaluators.length + (useAbsoluteScores ? 1 : 0)}" @click.stop="selectedTreeNode = null">
            <ul class="rubric-tools" v-if="rubric.useScores && rubric.useRelativeWeights">
                <li class="app-tool-item"><button class="btn-check" :class="{ checked: showScores }" @click.stop="showScores = !showScores"><span class="lbl-check" tabindex="-1"><i class="btn-icon-check fa" aria-hidden="true" />Cijferweergave</span></button></li>
            </ul>
            <div v-if="rubric.useScores && rubric.useRelativeWeights && showScores" class="treenode-weight-header rb-col-start-2">
                <span>{{ $t('weight') }}</span>
            </div>
            <ul class="rubric-header" :class="useScores && rubric.useRelativeWeights && showScores ? 'rb-col-start-3' : 'rb-col-start-2'" v-if="useScores || (useGrades && evaluators.length)">
                <li class="rubric-header-title mod-res" v-for="evaluator in evaluators"
                    :class="{ 'mod-grades': useGrades }" :title="evaluator.name" @click="copyRubricResults(evaluator.resultId)">{{ evaluator.name|capitalize }}</li>
                <li v-if="useAbsoluteScores" class="rubric-header-title mod-res mod-max">Max.</li>
            </ul>
            <ul class="rubric-header mod-date" :class="useScores && rubric.useRelativeWeights && showScores ? 'rb-col-start-3' : 'rb-col-start-2'" v-if="useScores || (useGrades && evaluators.length)">
                <li class="rubric-header-date" v-for="evaluator in evaluators"
                    :class="{ 'mod-grades': useGrades }" :title="evaluator.name">{{ evaluator.date|formatDate }}</li>
                <li v-if="useAbsoluteScores" class="rubric-header-date mod-max" aria-hidden="true"></li>
            </ul>
            <template v-for="{cluster, maxScore, evaluations} in getClusterRowsData(rubric)">
                <tree-node-title :rubric="rubric" :tree-node="cluster" :options="selectOptions"></tree-node-title>
                <tree-node-weight :rubric="rubric" :tree-node="cluster" :options="selectOptions" :show-scores="showScores"></tree-node-weight>
                <tree-node-rubric-results :rubric="rubric" :tree-node="cluster" :rubric-evaluation="rubricEvaluation" :max-score="maxScore" :options="selectOptions" :evaluations="evaluations" :show-scores="showScores"></tree-node-rubric-results>
                <template v-for="({category, maxScore, evaluations}, index) in getCategoryRowsData(cluster)">
                    <template v-if="category.title && rubric.getAllCriteria(category).length > 0">
                        <tree-node-title :rubric="rubric" :tree-node="category" :options="selectOptions"></tree-node-title>
                        <tree-node-weight :rubric="rubric" :tree-node="category" :options="selectOptions" :show-scores="showScores"></tree-node-weight>
                        <tree-node-rubric-results :rubric="rubric" :tree-node="category" :rubric-evaluation="rubricEvaluation" :max-score="maxScore" :options="selectOptions" :evaluations="evaluations" :show-scores="showScores"></tree-node-rubric-results>
                    </template>
                    <template v-for="{criterium, maxScore, evaluations} in getCriteriumRowsData(category)">
                        <tree-node-title :rubric="rubric" :tree-node="criterium" :options="selectOptions"></tree-node-title>
                        <tree-node-weight :rubric="rubric" :tree-node="criterium" :options="selectOptions" :show-scores="showScores"></tree-node-weight>
                        <tree-node-rubric-results :rubric="rubric" :tree-node="criterium" :rubric-evaluation="rubricEvaluation" :max-score="maxScore" :options="selectOptions" :evaluations="evaluations" :show-scores="showScores"></tree-node-rubric-results>
                    </template>
                    <div class="category-sep" v-if="index < getCategoryRowsData(cluster).length - 1"></div>
                </template>
                <div class="cluster-sep" :class="{ 'mod-hide-last': useGrades || (useScores && rubric.useRelativeWeights && !showScores) }"></div>
            </template>
            <template v-if="useScores && (!rubric.useRelativeWeights || showScores)">
                <div class="total-title" :class="{'mod-res-col': useScores && rubric.useRelativeWeights && showScores}">{{ $t('total') }} {{ $t('rubric') }}:</div>
                <tree-node-rubric-results :rubric="rubric" :tree-node="rubric" :rubric-evaluation="rubricEvaluation" :max-score="rubric.getMaximumScore()" :evaluations="evaluators" :show-scores="showScores"></tree-node-rubric-results>
            </template>
        </div>
        <tree-node-results-view v-if="selectOptions.selectedTreeNode" :rubric="rubric" :tree-node="selectOptions.selectedTreeNode" :evaluations="getTreeNodeRowData(selectOptions.selectedTreeNode).evaluations" @close="selectOptions.selectedTreeNode = null"></tree-node-results-view>
    </div>
    <div v-else class="alert-rubric-results">{{ $t('no-results-available') }}</div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric from '../Domain/Rubric';
    import TreeNode from '../Domain/TreeNode';
    import Cluster from '../Domain/Cluster';
    import Category from '../Domain/Category';
    import Criterium from '../Domain/Criterium';
    import RubricEvaluation from '../Domain/RubricEvaluation';
    import TreeNodeResultsView from '../Components/TreeNodeResultsView.vue';
    import TreeNodeRubricResults from '../Components/TreeNodeRubricResults.vue';
    import TreeNodeWeight from '../Components/TreeNodeWeight.vue';
    import TreeNodeTitle from '../Components/TreeNodeTitle.vue';

    function pad(num: number) : string {
        return `${num < 10 ? '0' : ''}${num}`;
    }

    @Component({
        components: { TreeNodeResultsView, TreeNodeRubricResults, TreeNodeWeight, TreeNodeTitle },
        filters: {
            capitalize: function (value: any) {
                if (!value) { return ''; }
                value = value.toString();
                return value.charAt(0).toUpperCase() + value.slice(1);
            },
            formatDate: function (s: string) {
                const date = new Date(s);
                if (isNaN(date.getDate())) { // todo: dates with timezone offsets, e.g. +0200 result in NaN data in Safari. For now, return an empty string.
                    return '';
                }
                return `${pad(date.getDate())}/${pad(date.getMonth() + 1)}/${date.getFullYear().toString().substr(-2)} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
            }
        }
    })
    export default class RubricResult extends Vue {
        private showScores = false;

        private selectOptions: any = {
            selectedTreeNode: null,
            highlightedTreeNode: null
        };

        @Prop({type: Rubric}) readonly rubric!: Rubric;
        @Prop({type: RubricEvaluation, required: true}) readonly rubricEvaluation!: RubricEvaluation;
        @Prop({type: Object, default: () => ({})}) readonly options!: any;
        @Prop({type: String, default: ''}) readonly rubricEntryUrl!: string;

        copyRubricResults(resultId: string) {
            if (this.rubricEntryUrl) {
                window.open(this.rubricEntryUrl + '&result_id=' + resultId, '_self');
            }
        }

        get selectedTreeNode(): Criterium|Category|Cluster|null {
            return this.selectOptions.selectedTreeNode;
        }

        set selectedTreeNode(treeNode: Criterium|Category|Cluster|null) {
            this.selectOptions.selectedTreeNode = treeNode;
        }

        get highlightedTreeNode(): Criterium|Category|Cluster|null {
            return this.selectOptions.highlightedTreeNode;
        }

        set highlightedTreeNode(treeNode: Criterium|Category|Cluster|null) {
            this.selectOptions.highlightedTreeNode = treeNode;
        }

        get useScores() {
            return this.rubric.useScores;
        }

        get useAbsoluteScores() {
            return this.rubric.useScores && !this.rubric.useRelativeWeights;
        }

        get useGrades() {
            return !this.rubric.useScores;
        }

        get evaluators() {
            return this.rubricEvaluation.getEvaluators();
        }

        getTreeNodeRowData(treeNode: TreeNode) {
            if (treeNode instanceof Criterium) {
                return this.getCriteriumRowData(treeNode);
            }
            if (treeNode instanceof Category) {
                return this.getCategoryRowData(treeNode);
            }
            if (treeNode instanceof Cluster) {
                return this.getClusterRowData(treeNode);
            }
            return { maxScore: 0, evaluations: [] };
        }

        getClusterRowsData(rubric: Rubric) {
            return rubric.clusters
                .filter(cluster => cluster.hasChildren())
                .map(this.getClusterRowData);
        }

        getClusterRowData(cluster: Cluster) {
            return {
                cluster,
                maxScore: this.rubric.getClusterMaxScore(cluster),
                evaluations: this.rubricEvaluation.getEvaluations(cluster)
            };
        }

        getCategoryRowsData(cluster: Cluster) {
            return cluster.categories
                .filter(category => category.hasChildren())
                .map(this.getCategoryRowData);
        }

        getCategoryRowData(category: Category) {
            return {
                category,
                maxScore: this.rubric.getCategoryMaxScore(category),
                evaluations: this.rubricEvaluation.getEvaluations(category)
            };
        }

        getCriteriumRowsData(category: Category) {
            return category.criteria.map(criterium => this.getCriteriumRowData(criterium));
        }

        getCriteriumRowData(criterium: Criterium) {
            return {
                criterium,
                maxScore: this.rubric.getCriteriumMaxScore(criterium),
                evaluations: this.rubricEvaluation.getEvaluations(criterium)
            };
        }
    }
</script>

<style lang="scss">
    .rubric-results-view {
        display: flex;
    }

    .rubric.mod-res {
        align-self: flex-start;
        grid-template-columns: minmax(max-content, 23rem) minmax(calc(var(--num-cols) * 6rem), calc(var(--num-cols) * 12rem));
    }

    .rubric.mod-res-w {
        align-self: flex-start;
        grid-template-columns: minmax(max-content, 23rem) 7rem minmax(calc(var(--num-cols) * 6rem), calc(var(--num-cols) * 12rem));
    }

    .rubric-header.mod-date {
        margin-top: -1.5rem;
        z-index: 29;
    }

    .rubric-header-title.mod-res {
        background-color: hsl(203, 38%, 53%);
        box-shadow: none;
        text-align: right;

        &.mod-grades {
            text-align: left;
        }

        &.mod-max {
            background: hsla(203, 33%, 60%, 1);
        }
    }

    .rubric-header-date {
        color: hsla(200, 30%, 40%, 1);
        flex: 1;
        font-size: 1.2rem;
        padding: 0 .5rem;
        text-align: right;

        &.mod-max {
            visibility: hidden;
        }

        &:not(:last-child) {
            margin-right: .7rem;
        }

        &.mod-grades {
            text-align: left;
        }
    }

    .total-title.mod-res-col {
        grid-column: 1 / -2;
    }
</style>

<style lang="scss" scoped>
    .treenode-feedback-icon.fa-info {
        margin-right: .5rem;
        font-size: 1.4rem;
    }

    .cluster-sep {
        border-color: #deebee;
        margin: 1rem 0 1.5rem;
    }

    .m-not-scored {
        color: hsl(190, 33%, 50%);
        font-size: 1.2rem;
        font-style: oblique;
    }
</style>