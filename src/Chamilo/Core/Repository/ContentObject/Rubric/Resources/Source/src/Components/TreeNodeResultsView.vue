<i18n>
{
    "en": {
        "chose": "chose",
        "close": "Close",
        "extra-feedback": "Extra feedback",
        "gave-score": "gave a score of",
        "level-descriptions": "Level descriptions",
        "score-weight": "Score on grand total with the given weight",
        "weight": "Weight"
    },
    "fr": {
        "chose": "a choisi",
        "close": "Fermer",
        "extra-feedback": "Feed-back supplémentaire",
        "gave-score": "a donné le score",
        "level-descriptions": "Descriptions de niveau",
        "score-weight": "Score sur le total général avec le poids donné",
        "weight": "Poids"
    },
    "nl": {
        "chose": "koos",
        "close": "Sluiten",
        "extra-feedback": "Extra feedback",
        "gave-score": "gaf score",
        "level-descriptions": "Niveauomschrijvingen",
        "score-weight": "Score op eindtotaal met het gegeven gewicht",
        "weight": "Gewicht"
    }
}
</i18n>
<template>
    <div class="selected-treenode-container">
        <div class="selected-treenode-wrapper">
            <button class="btn-info-close" :aria-label="$t('close')" :title="$t('close')" @click="$emit('close')"><i aria-hidden="true" class="fa fa-close"/></button>
            <div class="selected-treenode-results">
                <div class="selected-treenode-results-title u-markdown-criterium" v-html="treeNode.toMarkdown()"></div>
                <div v-if="rubric.useScores && rubric.useRelativeWeights" style="margin: 15px 0;">{{ $t('weight') }}: {{ relWeight|formatNum }}<i class="fa fa-percent" aria-hidden="true"></i><span class="sr-only">%</span></div>
                <div class="results-details" style="margin-top: 20px">
                    <div v-for="({evaluator, score, level, feedback}, index) in evaluations" style="display: flex;flex-direction: column">
                        <template v-if="isCriterium">
                            <div style="display: flex;justify-content: space-between;gap:1rem;align-items:baseline">
                                <div style="flex:1">
                                    <div style="padding: .2rem 0 0;line-height:1;"><strong style="font-size: 1.6rem;color:#486d84;">{{ evaluator.name|capitalize }}</strong></div>
                                    <div style="font-weight:400;font-size:1.2rem;color:#51788c">{{ new Date(evaluator.date)|formatDate }}</div>
                                </div>
                                <template v-if="rubric.useScores">
                                    <div v-if="rubric.useScores" style="white-space:nowrap;"><span style="font-size: 1.55rem;font-weight:400;">{{ score|formatNum }}<i class="fa fa-percent" v-if="rubric.useRelativeWeights" style="color: #666"></i></span><span v-if="rubric.useRelativeWeights" class="sr-only">%</span></div>
                                    <div v-if="rubric.useScores && rubric.useRelativeWeights" :title="$t('score-weight')"
                                         style="pointer-events: all; white-space:nowrap;width:60px;margin-left: -1.5rem;text-align:right;"><span style="font-weight: 400;border-bottom: 1px dotted #999">{{ calcWeightedScore(score)|formatNum }}<i class="fa fa-percent" v-if="rubric.useRelativeWeights" style="color: #666"></i></span><span v-if="rubric.useRelativeWeights" class="sr-only">%</span></div>
                                </template>
                            </div>
                            <span style="color: #808080;margin-top: .25rem">Niveau:</span>
                            <div style="display: flex;justify-content: space-between;gap:1rem;margin-bottom:1rem;pointer-events: all;" @click.stop="toggleIndex(index)">
                                <div style="font-weight: 400;flex:1;border-radius:3px;/*border-bottom: 1px solid #dee;*/">
                                    <div style="line-height:1.4;font-weight: 700;" :style="rubric.findChoice(treeNode, level).feedback ? 'cursor:pointer' : ''">{{ level.title }}</div>
                                </div>
                                <!--<div v-if="rubric.useScores && typeof score === 'number'" style="color:#333;white-space:nowrap;float:right;padding:0"><span style="display:block;margin-bottom:-1rem;font-size: 1.6rem;font-weight:400;white-space:nowrap">{{ score|formatNum }}<i class="fa fa-percent" v-if="rubric.useRelativeWeights" style="color: #666"></i></span><span v-if="rubric.useRelativeWeights" class="sr-only">%</span></div>
                                <div v-if="rubric.useScores && rubric.useRelativeWeights" :title="$t('score-weight')"
                                     style="pointer-events: all; white-space:nowrap; border-radius:3px;margin-left: .5rem;padding: 0;"><span style="font-weight: 400; border-bottom: 1px dotted #999;margin-bottom:-1rem">{{ calcWeightedScore(score)|formatNum }}<i class="fa fa-percent" v-if="rubric.useRelativeWeights" style="color: #666"></i></span><span v-if="rubric.useRelativeWeights" class="sr-only">%</span></div>
                                -->
                            </div>
                            <div v-if="indexes.indexOf(index) !== -1 && rubric.findChoice(treeNode, level).feedback" style="padding: 0 0.5rem;background: hsla(180,45%,98%, 1);margin-top: -.25rem;margin-left:-.5rem;margin-right:-.5rem;margin-bottom:1.2rem;border:1px solid hsla(180, 38%,94%,1)">{{ rubric.findChoice(treeNode, level).feedback }}</div>
                            <div v-if="feedback" style="margin-bottom: 10px;margin-top:-1rem;padding:.6rem 0;"><i class="fa fa-comment-o judge-comment" style="float: left"></i>{{feedback}}</div>
                        </template>
                        <template v-else>
                            <div style="margin-bottom:1rem;display: flex;justify-content: space-between;gap:1rem;align-items:baseline">
                                <div style="flex:1">
                                    <div style="padding: .2rem 0 0;line-height: 1;"><strong style="font-size: 1.6rem;color:#486d84;">{{ evaluator.name|capitalize }}</strong></div>
                                    <div style="font-weight:400;font-size: 1.2rem;color:#51788c">{{ new Date(evaluator.date)|formatDate }}</div>
                                </div>
                                <template v-if="rubric.useScores">
                                    <div v-if="rubric.useScores" style="white-space:nowrap;"><span style="font-size: 1.55rem;font-weight:400;">{{ score|formatNum }}<i class="fa fa-percent" v-if="rubric.useRelativeWeights" style="color: #666"></i></span><span v-if="rubric.useRelativeWeights" class="sr-only">%</span></div>
                                    <div v-if="rubric.useScores && rubric.useRelativeWeights" :title="$t('score-weight')"
                                         style="pointer-events: all; white-space:nowrap;width:60px;margin-left: -1.5rem;text-align:right;"><span style="font-weight: 400;border-bottom: 1px dotted #999">{{ calcWeightedScore(score)|formatNum }}<i class="fa fa-percent" v-if="rubric.useRelativeWeights" style="color: #666"></i></span><span v-if="rubric.useRelativeWeights" class="sr-only">%</span></div>
                                </template>
                            </div>
                            <div v-if="feedback" style="margin-bottom: 10px;margin-top:-1rem;padding:.6rem 0;"><i class="fa fa-comment-o judge-comment" style="float: left"></i>{{feedback}}</div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import {Component, Prop, Watch, Vue} from 'vue-property-decorator';
    import Rubric from '../Domain/Rubric';
    import Cluster from '../Domain/Cluster';
    import Category from '../Domain/Category';
    import Criterium from '../Domain/Criterium';

    function pad(num: number) : string {
        return `${num < 10 ? '0' : ''}${num}`;
    }

    @Component({
        filters: {
            capitalize: function (value: any) {
                if (!value) { return ''; }
                value = value.toString();
                return value.charAt(0).toUpperCase() + value.slice(1);
            },
            formatDate: function (date: Date) {
                if (isNaN(date.getDate())) { // todo: dates with timezone offsets, e.g. +0200 result in NaN data in Safari. For now, return an empty string.
                    return '';
                }
                return `${pad(date.getDate())}/${pad(date.getMonth() + 1)}/${date.getFullYear().toString().substr(-2)} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
            },
            formatNum: function (v: number|null) {
                if (v === null) { return '0'; }
                return v.toLocaleString(undefined, {maximumFractionDigits: 2});
            }
        }
    })
    export default class TreeNodeResultsView extends Vue {
        private indexes: number[] = [];

        @Prop({type: Rubric}) readonly rubric!: Rubric;
        @Prop({type: [Cluster, Category, Criterium]}) readonly treeNode!: Cluster|Category|Criterium;
        @Prop({type: Array}) readonly evaluations!: any[];

        toggleIndex(index: number) {
            console.log('toggle');
            if (this.indexes.indexOf(index) !== -1) {
                this.indexes.splice(this.indexes.indexOf(index), 1);
            } else {
                this.indexes.push(index);
            }
        }

        get isCriterium() {
            return this.treeNode instanceof Criterium;
        }

        get relWeight() {
            return this.rubric.getRelativeWeight(this.treeNode);
        }

        calcWeightedScore(score: number) {
            return this.relWeight * score / 100;
        }

        @Watch('treeNode')
        setTreeNode() {
            this.indexes = [];
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

    .choice-feedback {
        line-height: 1.5em;

        ul {
            list-style: disc;
        }

        ul, ol {
            margin: 0 0 0 2rem;
            padding: 0;
        }
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
        /*background: #e4e3e3;*/
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

    .results-details  {
        max-width: 500px;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-left: -.5rem;

        > div {
            /*padding-left: 1rem;
            padding-right: 1rem;*/
            /*padding-top: .5rem;*/
            padding: 1rem 1rem 0;
            /*border-top: 1px solid #eef6f6;*/
            border-radius: 3px;
        }

        > div:nth-child(odd) {
            background-color: hsla(210, 11%, 93%, .45);
        }

        span {
            font-weight: bold;

            &.score-title {
                color: hsla(191, 41%, 33%, 1);
            }
        }

        .judge-comment {
            color: hsl(190, 33%, 85%);
            font-size: 1.8rem;
            margin-top: .25rem;
            margin-right: 1rem;
        }
    }
</style>

<style scoped>
    .fa-percent {
        font-size: 1.1rem;
        color: #777;
        margin-left: .2rem;
    }
</style>