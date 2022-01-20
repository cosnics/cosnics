<i18n>
{
    "en": {
        "close": "Close",
        "weight": "Weight"
    },
    "fr": {
        "close": "Fermer",
        "weight": "Poids"
    },
    "nl": {
        "close": "Sluiten",
        "weight": "Gewicht"
    }
}
</i18n>
<template>
    <div class="selected-treenode-container" style="pointer-events: all">
        <div class="selected-treenode-wrapper">
            <button class="btn-info-close" :aria-label="$t('close')" :title="$t('close')" @click="$emit('close')"><i aria-hidden="true" class="fa fa-close"/></button>
            <div class="selected-treenode-results">
                <div class="selected-treenode-results-title u-markdown-criterium" v-html="treeNode.toMarkdown()"></div>
                <div class="selected-treenode-results-weight" v-if="rubric.useScores && rubric.useRelativeWeights">{{ $t('weight') }}: {{ relWeight|formatNum }}<i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></div>
                <div class="results-details">
                    <tree-node-evaluator-results v-for="({evaluator, score, level, feedback}, index) in evaluations" :key="`${index}-${treeNode.id}`" :rubric="rubric" :tree-node="treeNode" :evaluator="evaluator" :score="score" :level="level" :feedback="feedback" />
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Vue} from 'vue-property-decorator';
    import Rubric from '../Domain/Rubric';
    import Cluster from '../Domain/Cluster';
    import Category from '../Domain/Category';
    import Criterium from '../Domain/Criterium';
    import TreeNodeEvaluatorResults from './TreeNodeEvaluatorResults.vue';

    function pad(num: number) : string {
        return `${num < 10 ? '0' : ''}${num}`;
    }

    @Component({
        components: { TreeNodeEvaluatorResults },
        filters: {
            formatNum: function (v: number|null) {
                if (v === null) { return '0'; }
                return v.toLocaleString(undefined, {maximumFractionDigits: 2});
            }
        }
    })
    export default class TreeNodeResultsView extends Vue {
        @Prop({type: Rubric}) readonly rubric!: Rubric;
        @Prop({type: [Cluster, Category, Criterium]}) readonly treeNode!: Cluster|Category|Criterium;
        @Prop({type: Array}) readonly evaluations!: any[];

        get isCriterium() {
            return this.treeNode instanceof Criterium;
        }

        get relWeight() {
            return this.rubric.getRelativeWeight(this.treeNode);
        }
    }
</script>
<style lang="scss">
    .btn-info-close {
        align-items: center;
        background-color: $bg-criterium-details;
        border: 1px solid transparent;
        border-radius: $border-radius;
        color: #777;
        display: flex;
        float: right;
        height: 1.6em;
        justify-content: center;
        margin-left: .5em;
        margin-top: .3em;
        padding: 0;
        transition: background-color 200ms, color 200ms;
        width: 1.6em;

        &:hover {
            background-color: $btn-color;
            border: 1px solid transparent;
            border-radius: $border-radius;
            color: #fff;
        }

        &:focus {
            border: 1px solid $input-color-focus;
        }
    }

    .selected-treenode-container {
        margin-top: 1em;
    }

    .selected-treenode-wrapper {
        max-width: 80ch;
    }

    @media only screen and (min-width: 900px) {
        .btn-info-close {
            display: none;
        }

        .selected-treenode-container {
            border-left: 1px solid hsla(191, 21%, 80%, 1);
            margin-left: 1.5em;
            padding-left: 1.5em;
            width: 40%;
            pointer-events: none;
        }

        .selected-treenode-wrapper {
            position: -webkit-sticky;
            position: sticky;
            top: 10px;
        }
    }

    @media only screen and (max-width: 899px) {
        .selected-treenode-container {
            align-items: flex-start;
            background: hsla(0, 0, 0, .15);
            display: flex;
            height: 100%;
            justify-content: center;
            left: 0;
            margin-top: 0;
            overflow: auto;
            padding-top: 3em;
            pointer-events: none;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 10000;
        }

        .selected-treenode-wrapper {
            background: #fff;
            border-radius: $border-radius;
            box-shadow: 1px 1px 5px #999;
            margin: 0 1em;
            max-height: 90vh;
            padding: .5em;
            overflow-y: auto;
            pointer-events: all;
            width: 500px;
        }
    }

    .selected-treenode-results {
        border-radius: $border-radius;
        padding: .5em;
    }

    .selected-treenode-results-title {
        color: hsla(191, 41%, 38%, 1);
        font-size: 1.4rem;
        font-weight: 700;
        line-height: 1.3em;
        margin-bottom: .5em;
        max-width: 75ch;

        .separator {
            margin: 0 .3em;
        }
    }

    .selected-treenode-results-weight {
        margin: 15px 0;
    }

    .results-details  {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-left: -.5rem;
        margin-top: 20px;
        max-width: 500px;
    }

    .treenode-evaluator-results:nth-child(odd) {
        background-color: hsla(210, 11%, 93%, .45);
    }
</style>

<style scoped>
    .fa-percent {
        font-size: 1.1rem;
        color: #777;
        margin-left: .2rem;
    }
</style>